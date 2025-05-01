<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * View attendance session details
 *
 * @package    mod_attendance
 * @copyright  2023 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot.'/mod/attendance/lib.php');

$id = required_param('id', PARAM_INT);             // Course Module ID
$sessionid = required_param('sessionid', PARAM_INT); // Session ID

// Get course module
$cm = get_coursemodule_from_id('attendance', $id, 0, false, MUST_EXIST);
// Get course
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
// Get attendance instance
$attendance = $DB->get_record('attendance', ['id' => $cm->instance], '*', MUST_EXIST);

require_login($course, true, $cm);
$context = context_module::instance($cm->id);

// Check whether user can view data
$canview = has_capability('mod/attendance:viewreports', $context);
$canviewown = has_capability('mod/attendance:viewown', $context);

// If user can only view their own data, restrict view
if (!$canview && $canviewown) {
    // Get the logs for this session
    $logs = $DB->get_records('attendance_log', ['sessionid' => $sessionid, 'studentid' => $USER->id]);
    if (empty($logs)) {
        // No record for current user, display error
        print_error('nopermission', 'mod_attendance', new moodle_url('/mod/attendance/view.php', ['id' => $cm->id]));
    }
} else if (!$canview) {
    // User has no permission to view any data
    print_error('nopermission', 'mod_attendance', new moodle_url('/mod/attendance/view.php', ['id' => $cm->id]));
}

// Create attendance instance
$attendanceinstance = new mod_attendance\attendance($cm->id);

// Get the session
$session = $attendanceinstance->get_session($sessionid);
if (!$session) {
    print_error('invaliddata', 'error');
}

// Return URL
$returnurl = new moodle_url('/mod/attendance/view.php', ['id' => $cm->id]);

// Set page URL and title
$PAGE->set_url('/mod/attendance/view_session.php', ['id' => $id, 'sessionid' => $sessionid]);
$PAGE->set_title(format_string($attendance->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

// Get attendance logs for this session
$logs = $attendanceinstance->get_session_logs($sessionid);

// Start output
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('attendancerecords', 'mod_attendance') . ': ' . userdate($session->sessiondate));

// Display session details
echo html_writer::start_div('attendance-session-details');
echo html_writer::tag('p', get_string('sessiondate', 'mod_attendance') . ': ' . userdate($session->sessiondate));
echo html_writer::tag('p', get_string('sessionduration', 'mod_attendance') . ': ' . (int)$session->duration . ' ' . get_string('minutes'));
if (!empty($session->description)) {
    echo html_writer::tag('p', get_string('sessiondescription', 'mod_attendance') . ': ' . format_text(s($session->description), FORMAT_MOODLE));
}

// Show session lock status for teachers
if (has_capability('mod/attendance:managesessions', $context)) {
    $lockstatus = !empty($session->locked) ? 
        html_writer::tag('span', get_string('locked', 'mod_attendance'), ['class' => 'badge badge-warning']) :
        html_writer::tag('span', get_string('unlocked', 'mod_attendance'), ['class' => 'badge badge-success']);
    echo html_writer::tag('p', get_string('status', 'mod_attendance') . ': ' . $lockstatus);
}

echo html_writer::end_div();

// Create status options for display
$statusoptions = [
    0 => get_string('statusabsent', 'mod_attendance'),
    1 => get_string('statuspresent', 'mod_attendance'),
    2 => get_string('statuslate', 'mod_attendance'),
    3 => get_string('statusexcused', 'mod_attendance')
];

// Display attendance logs
if (empty($logs)) {
    echo $OUTPUT->notification(get_string('noattendancerecords', 'mod_attendance'), 'notifyinfo');
} else {
    // If student can only view their own attendance, filter the logs
    if (!$canview && $canviewown) {
        $filtered_logs = [];
        foreach ($logs as $log) {
            if ($log->studentid == $USER->id) {
                $filtered_logs[] = $log;
            }
        }
        $logs = $filtered_logs;
    }
    
    // Get enrolled students to ensure we have names for all logs
    $students = get_enrolled_users($context, 'mod/attendance:view', 0, 'u.id, u.firstname, u.lastname, u.email', null, 0, 0, true);
    
    // Create and display attendance table
    $table = new html_table();
    $table->head = [
        get_string('student', 'mod_attendance'),
        get_string('status', 'mod_attendance'),
        get_string('remarks', 'mod_attendance')
    ];
    $table->attributes['class'] = 'generaltable';
    
    foreach ($logs as $log) {
        if (isset($students[$log->studentid])) {
            $student = $students[$log->studentid];
            
            // Ensure status is valid
            $status = isset($statusoptions[$log->status]) ? 
                $statusoptions[$log->status] : 
                get_string('unknown', 'mod_attendance');
            
            // Properly sanitize and format remarks
            $remarks = '';
            if (!empty($log->remarks)) {
                // Sanitize the remarks text
                $remarks = s($log->remarks);
                // Format with linebreaks preserved
                $remarks = format_text($remarks, FORMAT_MOODLE);
            }
            
            $table->data[] = [
                s(fullname($student)),  // Sanitize student name
                $status,
                $remarks
            ];
        }
    }
    
    echo html_writer::table($table);
}

// Add back button
echo html_writer::div(
    $OUTPUT->single_button($returnurl, get_string('back'), 'get'),
    'attendance-back-button'
);

// If teacher, add link to audit logs for this session
if (has_capability('mod/attendance:viewreports', $context)) {
    $auditurl = new moodle_url('/mod/attendance/view.php', [
        'id' => $cm->id,
        'tab' => 'audit',
        'sessionid' => $sessionid
    ]);
    echo html_writer::div(
        html_writer::link($auditurl, get_string('viewaudit', 'mod_attendance')),
        'attendance-audit-link'
    );
}

echo $OUTPUT->footer(); 
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
 * Take attendance for a session
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
require_capability('mod/attendance:takeattendances', $context);

// Create attendance instance
$attendanceinstance = new mod_attendance\attendance($cm->id);

// Get the session
$session = $attendanceinstance->get_session($sessionid);
if (!$session) {
    print_error('invaliddata', 'error');
}

// Check if session is locked and user doesn't have capability to modify locked sessions
if (!empty($session->locked) && !has_capability('mod/attendance:changeattendances', $context)) {
    print_error('sessionislocked', 'mod_attendance', new moodle_url('/mod/attendance/view.php', ['id' => $cm->id]));
}

// Return URL
$returnurl = new moodle_url('/mod/attendance/view.php', ['id' => $cm->id]);

// Set page URL and title
$PAGE->set_url('/mod/attendance/take.php', ['id' => $id, 'sessionid' => $sessionid]);
$PAGE->set_title(format_string($attendance->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

// Process attendance submission
if (optional_param('submitattendance', 0, PARAM_INT) && confirm_sesskey()) {
    $attendancedata = optional_param_array('status', [], PARAM_INT);
    $remarks = optional_param_array('remarks', [], PARAM_RAW);
    $error = false;
    $errormessage = '';
    
    // Validate remarks
    if (!empty($remarks)) {
        foreach ($remarks as $studentid => $remark) {
            // Strip tags to prevent XSS
            $remarks[$studentid] = clean_param($remark, PARAM_TEXT);
            
            // Check for length
            if (core_text::strlen($remarks[$studentid]) > 255) {
                $error = true;
                $errormessage = get_string('remarksvalidationmaxlength', 'mod_attendance');
                break;
            }
            
            // Check for potential malicious content
            if (preg_match('/<script/i', $remark) || preg_match('/javascript:/i', $remark)) {
                $error = true;
                $errormessage = get_string('inputcontainsjavascript', 'mod_attendance');
                break;
            }
            
            if (preg_match('/<[a-z][^>]*>/i', $remark)) {
                $error = true;
                $errormessage = get_string('inputcontainshtml', 'mod_attendance');
                break;
            }
        }
    }
    
    // Validate attendance data
    foreach ($attendancedata as $studentid => $status) {
        // Validate status values (0-3)
        if (!in_array($status, [0, 1, 2, 3])) {
            $error = true;
            $errormessage = get_string('invalidstatus', 'mod_attendance');
            break;
        }
        
        // Validate student ID is an integer
        if (!is_numeric($studentid) || $studentid <= 0) {
            $error = true;
            $errormessage = get_string('invalidstudent', 'mod_attendance');
            break;
        }
    }
    
    if ($error) {
        // Show error and don't save
        redirect($PAGE->url, $errormessage, null, \core\output\notification::NOTIFY_ERROR);
    } else {
        // Process the data
        if ($attendanceinstance->take_attendance($sessionid, $attendancedata, $remarks)) {
            redirect($returnurl, get_string('attendancesuccess', 'mod_attendance'), null, \core\output\notification::NOTIFY_SUCCESS);
        } else {
            redirect($returnurl, get_string('attendancesavefail', 'mod_attendance'), null, \core\output\notification::NOTIFY_ERROR);
        }
    }
}

// Get enrolled students
$students = get_enrolled_users($context, 'mod/attendance:view', 0, 'u.id, u.firstname, u.lastname, u.email', null, 0, 0, true);

// Get existing attendance logs
$attendancelogs = $attendanceinstance->get_session_logs($sessionid);

// Prepare attendance logs by student ID for easier access
$logs = [];
if ($attendancelogs) {
    foreach ($attendancelogs as $log) {
        $logs[$log->studentid] = $log;
    }
}

// Start output
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('takeattendance', 'mod_attendance') . ': ' . userdate($session->sessiondate));

// Display session details
echo html_writer::start_div('attendance-session-details');
echo html_writer::tag('p', get_string('sessiondate', 'mod_attendance') . ': ' . userdate($session->sessiondate));
echo html_writer::tag('p', get_string('sessionduration', 'mod_attendance') . ': ' . $session->duration . ' ' . get_string('minutes'));
if (!empty($session->description)) {
    echo html_writer::tag('p', get_string('sessiondescription', 'mod_attendance') . ': ' . format_text($session->description, FORMAT_MOODLE));
}

// Display lock status if session is locked
if (!empty($session->locked)) {
    if (has_capability('mod/attendance:changeattendances', $context)) {
        echo html_writer::tag('div', 
            get_string('locked', 'mod_attendance') . ' - ' . get_string('modifiable', 'mod_attendance'),
            ['class' => 'alert alert-warning']
        );
    } else {
        // Should never reach here due to earlier check, but just in case
        redirect($returnurl, get_string('sessionislocked', 'mod_attendance'), null, \core\output\notification::NOTIFY_ERROR);
    }
}

echo html_writer::end_div();

// Display attendance form
echo html_writer::start_tag('form', ['method' => 'post', 'action' => $PAGE->url, 'id' => 'attendanceform']);
echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);
echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'submitattendance', 'value' => '1']);

// Add JavaScript for client-side validation
$PAGE->requires->js_init_code('
    document.getElementById("attendanceform").addEventListener("submit", function(e) {
        const remarks = document.querySelectorAll("textarea[name^=\'remarks\']");
        let error = false;
        let message = "";
        
        remarks.forEach(function(remark) {
            // Check length
            if (remark.value.length > 255) {
                error = true;
                message = "'.get_string('remarksvalidationmaxlength', 'mod_attendance').'";
                remark.classList.add("is-invalid");
            }
            
            // Check for HTML/JS
            if (remark.value.match(/<script/i) || remark.value.match(/javascript:/i)) {
                error = true;
                message = "'.get_string('inputcontainsjavascript', 'mod_attendance').'";
                remark.classList.add("is-invalid");
            }
            
            if (remark.value.match(/<[a-z][^>]*>/i)) {
                error = true;
                message = "'.get_string('inputcontainshtml', 'mod_attendance').'";
                remark.classList.add("is-invalid");
            }
        });
        
        if (error) {
            e.preventDefault();
            alert(message);
            return false;
        }
        
        return true;
    });
');

// Create status options
$statusoptions = [
    0 => get_string('statusabsent', 'mod_attendance'),
    1 => get_string('statuspresent', 'mod_attendance'),
    2 => get_string('statuslate', 'mod_attendance'),
    3 => get_string('statusexcused', 'mod_attendance')
];

if (empty($students)) {
    echo html_writer::tag('p', get_string('nostudentsenrolled', 'mod_attendance'));
} else {
    $table = new html_table();
    $table->head = [
        get_string('student', 'mod_attendance'),
        get_string('status', 'mod_attendance'),
        get_string('remarks', 'mod_attendance')
    ];
    $table->attributes['class'] = 'generaltable attendance-take';
    
    foreach ($students as $student) {
        $statusfield = html_writer::select(
            $statusoptions,
            'status[' . $student->id . ']',
            isset($logs[$student->id]) ? $logs[$student->id]->status : 1, // Default to present
            false
        );
        
        $remarksfield = html_writer::tag('textarea',
            isset($logs[$student->id]) ? s($logs[$student->id]->remarks) : '',
            [
                'name' => 'remarks[' . $student->id . ']', 
                'rows' => '1', 
                'cols' => '30', 
                'maxlength' => '255',
                'placeholder' => get_string('remarks', 'mod_attendance'),
                'class' => 'form-control'
            ]
        );
        
        $table->data[] = [
            fullname($student),
            $statusfield,
            $remarksfield
        ];
    }
    
    echo html_writer::table($table);
    
    echo html_writer::div(
        html_writer::empty_tag('input', [
            'type' => 'submit',
            'value' => get_string('saveattendance', 'mod_attendance'),
            'class' => 'btn btn-primary'
        ]),
        'attendance-submit'
    );
}

echo html_writer::end_tag('form');

echo $OUTPUT->footer(); 
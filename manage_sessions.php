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
 * Manage attendance sessions
 *
 * @package    mod_attendance
 * @copyright  2023 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot.'/mod/attendance/lib.php');
require_once($CFG->dirroot.'/mod/attendance/classes/form/session_form.php');

$id = required_param('id', PARAM_INT);           // Course Module ID
$action = required_param('action', PARAM_ALPHA); // Action to perform
$sessionid = optional_param('sessionid', 0, PARAM_INT); // Session ID if editing or deleting

// Get course module
$cm = get_coursemodule_from_id('attendance', $id, 0, false, MUST_EXIST);
// Get course
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
// Get attendance instance
$attendance = $DB->get_record('attendance', ['id' => $cm->instance], '*', MUST_EXIST);

require_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/attendance:managesessions', $context);

// Create attendance instance
$attendanceinstance = new mod_attendance\attendance($cm->id);

// Set page URL and title
$PAGE->set_url('/mod/attendance/manage_sessions.php', ['id' => $id, 'action' => $action, 'sessionid' => $sessionid]);
$PAGE->set_title(format_string($attendance->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

// Initialize form
$session = null;
if ($sessionid && ($action === 'edit' || $action === 'delete')) {
    $session = $attendanceinstance->get_session($sessionid);
    if (!$session) {
        print_error('invaliddata', 'error');
    }
}

$returnurl = new moodle_url('/mod/attendance/view.php', ['id' => $cm->id]);

// Handle delete action
if ($action === 'delete' && $session) {
    // Check sesskey if form submitted
    if (optional_param('confirm', 0, PARAM_INT) && confirm_sesskey()) {
        if ($attendanceinstance->delete_session($sessionid)) {
            redirect($returnurl, get_string('sessiondeleted', 'mod_attendance'), null, \core\output\notification::NOTIFY_SUCCESS);
        } else {
            redirect($returnurl, get_string('error'), null, \core\output\notification::NOTIFY_ERROR);
        }
    }
    
    // Show confirmation dialog
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('deletesession', 'mod_attendance'));
    
    $confirmurl = new moodle_url('/mod/attendance/manage_sessions.php', [
        'id' => $cm->id,
        'action' => 'delete',
        'sessionid' => $sessionid,
        'confirm' => 1,
        'sesskey' => sesskey()
    ]);
    
    echo $OUTPUT->confirm(
        get_string('confirmdeletesession', 'mod_attendance'),
        $confirmurl,
        $returnurl
    );
    
    echo $OUTPUT->footer();
    exit;
}

// Initialize form
$formaction = null;
$formtitle = '';
switch ($action) {
    case 'add':
        $formaction = new moodle_url('/mod/attendance/manage_sessions.php', ['id' => $cm->id, 'action' => 'add']);
        $formtitle = get_string('addsession', 'mod_attendance');
        break;
    case 'edit':
        $formaction = new moodle_url('/mod/attendance/manage_sessions.php', ['id' => $cm->id, 'action' => 'edit', 'sessionid' => $sessionid]);
        $formtitle = get_string('editsession', 'mod_attendance');
        break;
    default:
        print_error('invalidaction', 'error');
}

// Create form
$formdata = null;
if ($session) {
    $formdata = clone $session;
}
$mform = new mod_attendance_session_form($formaction, ['cm' => $cm, 'session' => $formdata]);

// Form processing and displaying
if ($mform->is_cancelled()) {
    redirect($returnurl);
} else if ($formdata = $mform->get_data()) {
    // Process validated data
    if ($action === 'add') {
        if ($attendanceinstance->add_session($formdata)) {
            redirect($returnurl, get_string('sessionsaved', 'mod_attendance'), null, \core\output\notification::NOTIFY_SUCCESS);
        } else {
            redirect($returnurl, get_string('error'), null, \core\output\notification::NOTIFY_ERROR);
        }
    } else if ($action === 'edit') {
        if ($attendanceinstance->update_session($formdata)) {
            redirect($returnurl, get_string('sessionsaved', 'mod_attendance'), null, \core\output\notification::NOTIFY_SUCCESS);
        } else {
            redirect($returnurl, get_string('error'), null, \core\output\notification::NOTIFY_ERROR);
        }
    }
}

// Display the form
echo $OUTPUT->header();
echo $OUTPUT->heading($formtitle);
$mform->display();
echo $OUTPUT->footer(); 
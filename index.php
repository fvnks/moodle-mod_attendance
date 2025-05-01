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
 * Display information about all the mod_attendance modules in the requested course.
 *
 * @package    mod_attendance
 * @copyright  2023 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');

$id = required_param('id', PARAM_INT); // Course ID

// Ensure that the course specified is valid
$course = $DB->get_record('course', ['id' => $id], '*', MUST_EXIST);

// Course module context
$coursecontext = context_course::instance($course->id);

require_login($course);
require_capability('mod/attendance:view', $coursecontext);

$PAGE->set_url('/mod/attendance/index.php', ['id' => $id]);
$PAGE->set_title(format_string($course->fullname));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($coursecontext);

// Get all required strings
$strattendances = get_string('modulenameplural', 'mod_attendance');
$strname = get_string('name');
$strsessions = get_string('sessions', 'mod_attendance');
$strdescription = get_string('description');

echo $OUTPUT->header();
echo $OUTPUT->heading($strattendances);

// Get all attendance instances in this course
$attendances = get_all_instances_in_course('attendance', $course);

if (empty($attendances)) {
    notice(get_string('nothingtodisplay'), new moodle_url('/course/view.php', ['id' => $course->id]));
    exit;
}

$table = new html_table();
$table->attributes['class'] = 'generaltable mod_index';

$table->head = [$strname, $strsessions, $strdescription];
$table->align = ['left', 'center', 'left'];

// Display the table
foreach ($attendances as $attendance) {
    $cmid = $attendance->coursemodule;
    $cm = get_coursemodule_from_id('attendance', $cmid);
    $context = context_module::instance($cm->id);
    
    $viewurl = new moodle_url('/mod/attendance/view.php', ['id' => $cmid]);
    $attendancename = format_string($attendance->name, true, ['context' => $context]);
    
    // Count sessions
    $sessions = $DB->count_records('attendance_sessions', ['attendanceid' => $attendance->id]);
    
    $link = html_writer::link($viewurl, $attendancename);
    
    $description = format_module_intro('attendance', $attendance, $cmid);
    
    $table->data[] = [$link, $sessions, $description];
}

echo html_writer::table($table);
echo $OUTPUT->footer(); 
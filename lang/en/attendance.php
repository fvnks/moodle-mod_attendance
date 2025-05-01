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
 * Language strings for the attendance module
 *
 * @package    mod_attendance
 * @copyright  2023 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['modulename'] = 'Attendance';
$string['modulenameplural'] = 'Attendances';
$string['modulename_help'] = 'The attendance activity module allows teachers to take attendance in class and students to view their attendance record.';
$string['pluginname'] = 'Attendance';
$string['pluginadministration'] = 'Attendance administration';

$string['attendance'] = 'Attendance';
$string['attendance:addinstance'] = 'Add a new attendance activity';
$string['attendance:managesessions'] = 'Manage attendance sessions';
$string['attendance:takeattendances'] = 'Take attendances';
$string['attendance:view'] = 'View attendance information';
$string['attendance:viewown'] = 'View own attendance records';
$string['attendance:viewreports'] = 'View attendance reports';
$string['attendance:changeattendances'] = 'Change existing attendance records';

$string['attendanceforthecourse'] = 'Attendance for {$a}';
$string['addsession'] = 'Add session';
$string['editsession'] = 'Edit session';
$string['deletesession'] = 'Delete session';
$string['confirmdeletesession'] = 'Are you sure you want to delete this session?';

$string['sessiondate'] = 'Session date';
$string['sessiondescription'] = 'Description';
$string['sessionduration'] = 'Duration (minutes)';
$string['sessions'] = 'Sessions';
$string['sessionslist'] = 'Sessions list';
$string['nosessionsfound'] = 'No sessions found';
$string['sessionsaved'] = 'Session successfully saved';
$string['sessiondeleted'] = 'Session successfully deleted';
$string['sessiondateinpast'] = 'Session date cannot be in the past';
$string['durationmustbepositive'] = 'Duration must be a positive number';

$string['attendancestatus'] = 'Attendance status';
$string['status'] = 'Status';
$string['statuspresent'] = 'Present';
$string['statusabsent'] = 'Absent';
$string['statuslate'] = 'Late';
$string['statusexcused'] = 'Excused';
$string['remarks'] = 'Remarks';

$string['attendancerecords'] = 'Attendance records';
$string['noattendancerecords'] = 'No attendance records found';
$string['takeattendance'] = 'Take attendance';
$string['saveattendance'] = 'Save attendance';
$string['attendancereport'] = 'Attendance report';
$string['yourattendance'] = 'Your attendance';
$string['summary'] = 'Summary';

$string['attendancesuccess'] = 'Attendance has been successfully taken';
$string['attendancesavefail'] = 'Failed to save attendance data';

$string['student'] = 'Student';
$string['studentsenrolled'] = 'Students enrolled';
$string['nostudentsenrolled'] = 'No students enrolled';
$string['presentpercentage'] = 'Present (%)';
$string['absentpercentage'] = 'Absent (%)';
$string['latepercentage'] = 'Late (%)';
$string['excusedpercentage'] = 'Excused (%)';
$string['totalsessions'] = 'Total sessions';
$string['unknown'] = 'Unknown';
$string['back'] = 'Back';

$string['eventsessioncreated'] = 'Session created';
$string['eventsessionupdated'] = 'Session updated';
$string['eventsessiondeleted'] = 'Session deleted';
$string['eventattendancetaken'] = 'Attendance taken';

// Session locking
$string['locked'] = 'Locked';
$string['unlocked'] = 'Unlocked';
$string['modifiable'] = 'Modifiable';
$string['locksession'] = 'Lock';
$string['unlocksession'] = 'Unlock';
$string['locksessionhelp'] = 'Lock this session to prevent further changes from regular teachers';
$string['unlocksessionhelp'] = 'Unlock this session to allow changes';
$string['sessionlocked'] = 'Session has been locked successfully';
$string['sessionunlocked'] = 'Session has been unlocked successfully';
$string['sessionlockfailed'] = 'Failed to lock session';
$string['sessionunlockfailed'] = 'Failed to unlock session';

// Audit log
$string['auditlog'] = 'Audit log';
$string['noauditlogs'] = 'No audit logs found';
$string['viewaudit'] = 'View audit';
$string['date'] = 'Date';
$string['statusbefore'] = 'Previous status';
$string['statusafter'] = 'New status';
$string['remarksbefore'] = 'Previous remarks';
$string['remarksafter'] = 'New remarks';
$string['ipaddress'] = 'IP Address';
$string['filtersession'] = 'Filter by session';
$string['allsessions'] = 'All sessions';
$string['filter'] = 'Filter';
$string['none'] = 'None';

// Input validation
$string['remarksvalidationmaxlength'] = 'Remarks cannot exceed 255 characters';
$string['remarksvalidationnohtml'] = 'HTML is not allowed in remarks';
$string['nopermission'] = 'You do not have permission to view this page';
$string['maxlengthexceeded'] = 'Maximum length exceeded';
$string['inputcontainsjavascript'] = 'Input contains potentially dangerous JavaScript code';
$string['inputcontainshtml'] = 'Input contains potentially dangerous HTML code';
$string['invalidstatus'] = 'Invalid attendance status';
$string['invalidstudent'] = 'Invalid student';

// Settings page
$string['generalsettings'] = 'General settings';
$string['configintro'] = 'These settings allow you to customize the general behavior of the attendance module.';
$string['minimumattendance'] = 'Minimum attendance percentage';
$string['minimumattendance_desc'] = 'Minimum attendance percentage required to pass the course (used in reports and alerts).';
$string['autolock'] = 'Auto-lock sessions';
$string['autolock_desc'] = 'If enabled, sessions will be automatically locked after a certain time.';
$string['autolockdays'] = 'Days for auto-lock';
$string['autolockdays_desc'] = 'Number of days after the session date to automatically lock it.';

$string['statusconfig'] = 'Attendance status configuration';
$string['statusconfig_desc'] = 'Here you can configure the available statuses and their values.';
$string['enable'] = 'Enable';
$string['enablestatus_desc'] = 'Check to enable the attendance status "{$a}".';
$string['points'] = 'Points for';
$string['points_desc'] = 'Point value for the status "{$a}".';

$string['reportsettings'] = 'Report settings';
$string['reportsettings_desc'] = 'Configure how attendance reports are displayed.';
$string['attendancealert'] = 'Low attendance alert';
$string['attendancealert_desc'] = 'Highlight students with attendance below the minimum configured percentage.';
$string['alertcolor'] = 'Alert color';
$string['alertcolor_desc'] = 'Color to highlight students with low attendance.';

// Scheduled task
$string['autolockscheduledtask'] = 'Auto-lock attendance sessions';
$string['checkupdatesscheduledtask'] = 'Automatic GitHub update check';

// GitHub update settings
$string['githubupdatesettings'] = 'GitHub update settings';
$string['githubupdatesettings_desc'] = 'Configure how the plugin checks for updates from a GitHub repository.';
$string['enableupdatecheck'] = 'Enable update check';
$string['enableupdatecheck_desc'] = 'If enabled, the plugin will periodically check if updates are available on GitHub.';
$string['githubowner'] = 'GitHub repository owner';
$string['githubowner_desc'] = 'Username or organization that owns the repository (e.g., "username").';
$string['githubrepo'] = 'Repository name';
$string['githubrepo_desc'] = 'Name of the GitHub repository where the plugin is hosted (e.g., "moodle-mod_attendance").';
$string['updatecheckfrequency'] = 'Check frequency';
$string['updatecheckfrequency_desc'] = 'How often to check for available updates.';
$string['daily'] = 'Daily';
$string['weekly'] = 'Weekly';
$string['monthly'] = 'Monthly';
$string['checkupdates'] = 'Check for updates';
$string['checkupdates_desc'] = 'Manually check if updates are available for the plugin.';
$string['checkupdatesnow'] = 'Check for updates now';
$string['updateavailable'] = 'A new version ({$a->latest_version}) of the attendance plugin is available. You are currently using version {$a->current_version}. <a href="{$a->download_url}" target="_blank">Download update</a>';
$string['noupdatesavailable'] = 'You are already using the latest version of the attendance plugin.';
$string['updatecheckerror'] = 'Could not check for available updates. Verify the repository configuration.';
$string['issuetracker'] = 'Report an issue';
$string['issuetracker_desc'] = 'If you find an issue with the plugin, you can report it on the GitHub issue tracker.';
$string['reportissue'] = 'Report an issue on GitHub';
$string['sessionislocked'] = 'This session is locked and cannot be modified'; 
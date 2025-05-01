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
 * Prints a particular instance of attendance
 *
 * @package    mod_attendance
 * @copyright  2023 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once($CFG->libdir.'/tablib.php');

$id = required_param('id', PARAM_INT); // Course Module ID
$action = optional_param('action', '', PARAM_ALPHA); // Action if any
$sessionid = optional_param('sessionid', 0, PARAM_INT); // Session ID for actions

// Get course module
$cm = get_coursemodule_from_id('attendance', $id, 0, false, MUST_EXIST);
// Get course
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
// Get attendance instance
$attendance = $DB->get_record('attendance', ['id' => $cm->instance], '*', MUST_EXIST);

require_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/attendance:view', $context);

// Create attendance instance
$attendanceinstance = new mod_attendance\attendance($cm->id);

// Process actions if user has appropriate permissions
if ($action && confirm_sesskey() && $sessionid) {
    if ($action === 'locksession' && has_capability('mod/attendance:managesessions', $context)) {
        if ($attendanceinstance->lock_session($sessionid)) {
            redirect($PAGE->url, get_string('sessionlocked', 'mod_attendance'), null, \core\output\notification::NOTIFY_SUCCESS);
        } else {
            redirect($PAGE->url, get_string('sessionlockfailed', 'mod_attendance'), null, \core\output\notification::NOTIFY_ERROR);
        }
    } else if ($action === 'unlocksession' && has_capability('mod/attendance:changeattendances', $context)) {
        if ($attendanceinstance->unlock_session($sessionid)) {
            redirect($PAGE->url, get_string('sessionunlocked', 'mod_attendance'), null, \core\output\notification::NOTIFY_SUCCESS);
        } else {
            redirect($PAGE->url, get_string('sessionunlockfailed', 'mod_attendance'), null, \core\output\notification::NOTIFY_ERROR);
        }
    }
}

// Set page URL and title
$PAGE->set_url('/mod/attendance/view.php', ['id' => $cm->id]);
$PAGE->set_title(format_string($attendance->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

// Initialize output
$output = $PAGE->get_renderer('mod_attendance');

// Get current tab
$tab = optional_param('tab', 'sessions', PARAM_ALPHA);

// Get sessions
$sessions = $attendanceinstance->get_sessions();

// Start output
echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($attendance->name));

// Display intro if any
if (!empty($attendance->intro)) {
    echo $OUTPUT->box(format_module_intro('attendance', $attendance, $cm->id), 'generalbox mod_introbox', 'attendanceintro');
}

// Display navigation tabs
$tabs = [];
$tabs[] = new tabobject('sessions', new moodle_url('/mod/attendance/view.php', ['id' => $cm->id, 'tab' => 'sessions']), get_string('sessions', 'mod_attendance'));

// If user can view reports, add that tab
if (has_capability('mod/attendance:viewreports', $context)) {
    $tabs[] = new tabobject('report', new moodle_url('/mod/attendance/view.php', ['id' => $cm->id, 'tab' => 'report']), get_string('attendancereport', 'mod_attendance'));
}

// If user has capability to view audit logs, add that tab
if (has_capability('mod/attendance:viewreports', $context)) {
    $tabs[] = new tabobject('audit', new moodle_url('/mod/attendance/view.php', ['id' => $cm->id, 'tab' => 'audit']), get_string('auditlog', 'mod_attendance'));
}

echo $OUTPUT->tabtree($tabs, $tab);

// Display content based on selected tab
switch ($tab) {
    case 'sessions':
        // Add session button for teachers
        if (has_capability('mod/attendance:managesessions', $context)) {
            $addurl = new moodle_url('/mod/attendance/manage_sessions.php', ['id' => $cm->id, 'action' => 'add']);
            echo html_writer::div(
                $OUTPUT->single_button($addurl, get_string('addsession', 'mod_attendance')),
                'attendance-add-session'
            );
        }
        
        // Display sessions list
        if (empty($sessions)) {
            echo $OUTPUT->notification(get_string('nosessionsfound', 'mod_attendance'), 'notifyinfo');
        } else {
            // Create and display sessions table
            $table = new html_table();
            $table->attributes['class'] = 'generaltable';
            $table->head = [
                get_string('sessiondate', 'mod_attendance'),
                get_string('sessiondescription', 'mod_attendance'),
                get_string('sessionduration', 'mod_attendance'),
                get_string('status', 'mod_attendance'),
                get_string('actions')
            ];
            
            foreach ($sessions as $session) {
                $actions = '';
                
                // Lock status indication
                $statustext = '';
                if (!empty($session->locked)) {
                    $statustext = html_writer::tag('span', get_string('locked', 'mod_attendance'), ['class' => 'badge badge-warning']);
                } else {
                    $statustext = html_writer::tag('span', get_string('unlocked', 'mod_attendance'), ['class' => 'badge badge-success']);
                }
                
                // Session is locked, but user can change attendance
                if (!empty($session->locked) && has_capability('mod/attendance:changeattendances', $context)) {
                    $statustext .= ' ' . html_writer::tag('span', get_string('modifiable', 'mod_attendance'), ['class' => 'badge badge-info']);
                }
                
                // Add lock/unlock actions for users with appropriate permissions
                if (has_capability('mod/attendance:managesessions', $context) && empty($session->locked)) {
                    // Lock button
                    $lockurl = new moodle_url('/mod/attendance/view.php', [
                        'id' => $cm->id,
                        'sessionid' => $session->id,
                        'action' => 'locksession',
                        'sesskey' => sesskey()
                    ]);
                    $statustext .= ' ' . html_writer::link($lockurl, get_string('locksession', 'mod_attendance'), 
                        ['class' => 'btn btn-sm btn-warning', 'title' => get_string('locksessionhelp', 'mod_attendance')]);
                } else if (has_capability('mod/attendance:changeattendances', $context) && !empty($session->locked)) {
                    // Unlock button - only for users with higher permissions
                    $unlockurl = new moodle_url('/mod/attendance/view.php', [
                        'id' => $cm->id,
                        'sessionid' => $session->id,
                        'action' => 'unlocksession',
                        'sesskey' => sesskey()
                    ]);
                    $statustext .= ' ' . html_writer::link($unlockurl, get_string('unlocksession', 'mod_attendance'),
                        ['class' => 'btn btn-sm btn-danger', 'title' => get_string('unlocksessionhelp', 'mod_attendance')]);
                }
                
                // Take attendance link - teachers only
                if (has_capability('mod/attendance:takeattendances', $context)) {
                    // Don't allow taking attendance if locked (unless user can change)
                    if (empty($session->locked) || has_capability('mod/attendance:changeattendances', $context)) {
                        $takeurl = new moodle_url('/mod/attendance/take.php', [
                            'id' => $cm->id,
                            'sessionid' => $session->id
                        ]);
                        $actions .= html_writer::link($takeurl, get_string('takeattendance', 'mod_attendance'));
                        $actions .= ' | ';
                    }
                }
                
                // View session log
                $viewurl = new moodle_url('/mod/attendance/view_session.php', [
                    'id' => $cm->id,
                    'sessionid' => $session->id
                ]);
                $actions .= html_writer::link($viewurl, get_string('view'));
                
                // View audit logs for this session if user has capability
                if (has_capability('mod/attendance:viewreports', $context)) {
                    $auditurl = new moodle_url('/mod/attendance/view.php', [
                        'id' => $cm->id,
                        'tab' => 'audit',
                        'sessionid' => $session->id
                    ]);
                    $actions .= ' | ' . html_writer::link($auditurl, get_string('viewaudit', 'mod_attendance'));
                }
                
                // Edit and delete links for teachers with manage capability
                if (has_capability('mod/attendance:managesessions', $context)) {
                    // Only allow edit/delete if not locked or has higher permission
                    if (empty($session->locked) || has_capability('mod/attendance:changeattendances', $context)) {
                        $editurl = new moodle_url('/mod/attendance/manage_sessions.php', [
                            'id' => $cm->id,
                            'sessionid' => $session->id,
                            'action' => 'edit'
                        ]);
                        $actions .= ' | ' . html_writer::link($editurl, get_string('edit'));
                        
                        $deleteurl = new moodle_url('/mod/attendance/manage_sessions.php', [
                            'id' => $cm->id,
                            'sessionid' => $session->id,
                            'action' => 'delete'
                        ]);
                        $actions .= ' | ' . html_writer::link($deleteurl, get_string('delete'));
                    }
                }
                
                $table->data[] = [
                    userdate($session->sessiondate),
                    format_text($session->description, FORMAT_MOODLE),
                    $session->duration . ' ' . get_string('minutes'),
                    $statustext,
                    $actions
                ];
            }
            
            echo html_writer::table($table);
        }
        break;
        
    case 'report':
        // Check if user has view reports capability
        if (!has_capability('mod/attendance:viewreports', $context)) {
            // If student, show only their own report
            if (has_capability('mod/attendance:viewown', $context)) {
                $userid = $USER->id;
                $studentinfo = $DB->get_record('user', ['id' => $userid], 'id, firstname, lastname, email');
                $summary = [];
                
                // Get student summary
                $studentsummary = $attendanceinstance->get_student_summary($userid);
                
                if ($studentsummary) {
                    // Show individual report
                    echo $OUTPUT->heading(get_string('yourattendance', 'mod_attendance'));
                    
                    $table = new html_table();
                    $table->attributes['class'] = 'generaltable';
                    $table->head = [
                        get_string('sessiondate', 'mod_attendance'),
                        get_string('status', 'mod_attendance'),
                        get_string('remarks', 'mod_attendance')
                    ];
                    
                    // Get status options for display
                    $statusoptions = [
                        0 => get_string('statusabsent', 'mod_attendance'),
                        1 => get_string('statuspresent', 'mod_attendance'),
                        2 => get_string('statuslate', 'mod_attendance'),
                        3 => get_string('statusexcused', 'mod_attendance')
                    ];
                    
                    // Get all sessions
                    $sessions = $attendanceinstance->get_sessions();
                    
                    // Get all logs for this student
                    $logs = [];
                    foreach ($sessions as $session) {
                        $sessionlogs = $attendanceinstance->get_session_logs($session->id);
                        foreach ($sessionlogs as $log) {
                            if ($log->studentid == $userid) {
                                $logs[$session->id] = $log;
                            }
                        }
                    }
                    
                    foreach ($sessions as $session) {
                        $status = isset($logs[$session->id]) ? 
                            $statusoptions[$logs[$session->id]->status] : 
                            get_string('statusabsent', 'mod_attendance');
                        
                        $remarks = isset($logs[$session->id]) ? 
                            format_text($logs[$session->id]->remarks, FORMAT_MOODLE) : '';
                        
                        $table->data[] = [
                            userdate($session->sessiondate),
                            $status,
                            $remarks
                        ];
                    }
                    
                    echo html_writer::table($table);
                    
                    // Display summary
                    echo $OUTPUT->heading(get_string('summary', 'mod_attendance'));
                    $summarytable = new html_table();
                    $summarytable->attributes['class'] = 'generaltable';
                    $summarytable->head = [
                        get_string('presentpercentage', 'mod_attendance'),
                        get_string('absentpercentage', 'mod_attendance'),
                        get_string('latepercentage', 'mod_attendance'),
                        get_string('excusedpercentage', 'mod_attendance'),
                        get_string('totalsessions', 'mod_attendance')
                    ];
                    
                    // Calculate percentages
                    $presentpercent = ($studentsummary->total > 0) ? 
                        round(($studentsummary->present / $studentsummary->total) * 100) : 0;
                    $absentpercent = ($studentsummary->total > 0) ? 
                        round(($studentsummary->absent / $studentsummary->total) * 100) : 0;
                    $latepercent = ($studentsummary->total > 0) ? 
                        round(($studentsummary->late / $studentsummary->total) * 100) : 0;
                    $excusedpercent = ($studentsummary->total > 0) ? 
                        round(($studentsummary->excused / $studentsummary->total) * 100) : 0;
                    
                    $summarytable->data[] = [
                        $presentpercent . '%',
                        $absentpercent . '%',
                        $latepercent . '%',
                        $excusedpercent . '%',
                        $studentsummary->total
                    ];
                    
                    echo html_writer::table($summarytable);
                } else {
                    echo $OUTPUT->notification(get_string('noattendancerecords', 'mod_attendance'), 'notifyinfo');
                }
            } else {
                echo $OUTPUT->notification(get_string('nopermission', 'error'), 'notifyproblem');
            }
            break;
        }
        
        // For teachers - display complete attendance report
        $summary = $attendanceinstance->get_course_summary();
        
        if (empty($summary)) {
            echo $OUTPUT->notification(get_string('nostudentsenrolled', 'mod_attendance'), 'notifyinfo');
        } else {
            // Create and display report table
            $table = new html_table();
            $table->attributes['class'] = 'generaltable';
            $table->head = [
                get_string('student', 'mod_attendance'),
                get_string('presentpercentage', 'mod_attendance'),
                get_string('absentpercentage', 'mod_attendance'),
                get_string('latepercentage', 'mod_attendance'),
                get_string('excusedpercentage', 'mod_attendance'),
                get_string('totalsessions', 'mod_attendance')
            ];
            
            foreach ($summary as $student) {
                $table->data[] = [
                    fullname($student),
                    $student->presentpercent . '%',
                    $student->absentpercent . '%',
                    $student->latepercent . '%',
                    $student->excusedpercent . '%',
                    $student->total
                ];
            }
            
            echo html_writer::table($table);
        }
        break;

    case 'audit':
        // Check if user has view reports capability
        if (!has_capability('mod/attendance:viewreports', $context)) {
            echo $OUTPUT->notification(get_string('nopermission', 'error'), 'notifyproblem');
            break;
        }
        
        // Filter by session if provided
        $sessionfilter = optional_param('sessionid', 0, PARAM_INT);
        
        // Display filter form
        $sessions = $attendanceinstance->get_sessions();
        
        echo html_writer::start_tag('form', ['method' => 'get', 'action' => $PAGE->url]);
        echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'id', 'value' => $cm->id]);
        echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'tab', 'value' => 'audit']);
        
        $select = html_writer::select(
            [0 => get_string('allsessions', 'mod_attendance')] + 
            array_map(function($s) { 
                return userdate($s->sessiondate); 
            }, $sessions),
            'sessionid',
            $sessionfilter,
            false
        );
        
        echo html_writer::div(
            get_string('filtersession', 'mod_attendance') . ': ' . $select . ' ' .
            html_writer::empty_tag('input', [
                'type' => 'submit',
                'value' => get_string('filter', 'mod_attendance'),
                'class' => 'btn btn-secondary'
            ]),
            'attendance-filter'
        );
        echo html_writer::end_tag('form');
        
        // Get audit logs
        $auditlogs = [];
        
        if ($sessionfilter) {
            // Get audit logs for specific session
            $auditlogs = $attendanceinstance->get_session_audit_logs($sessionfilter);
        } else {
            // Get all audit logs
            foreach ($sessions as $session) {
                $sessionlogs = $attendanceinstance->get_session_audit_logs($session->id);
                $auditlogs = array_merge($auditlogs, $sessionlogs);
            }
            
            // Sort by time created (most recent first)
            usort($auditlogs, function($a, $b) {
                return $b->timecreated - $a->timecreated;
            });
        }
        
        if (empty($auditlogs)) {
            echo $OUTPUT->notification(get_string('noauditlogs', 'mod_attendance'), 'notifyinfo');
        } else {
            // Create and display audit log table
            $table = new html_table();
            $table->attributes['class'] = 'generaltable';
            $table->head = [
                get_string('date', 'mod_attendance'),
                get_string('sessiondate', 'mod_attendance'),
                get_string('student', 'mod_attendance'),
                get_string('user'),
                get_string('statusbefore', 'mod_attendance'),
                get_string('statusafter', 'mod_attendance'),
                get_string('remarksbefore', 'mod_attendance'),
                get_string('remarksafter', 'mod_attendance'),
                get_string('ipaddress', 'mod_attendance')
            ];
            
            // Get status options for display
            $statusoptions = [
                0 => get_string('statusabsent', 'mod_attendance'),
                1 => get_string('statuspresent', 'mod_attendance'),
                2 => get_string('statuslate', 'mod_attendance'),
                3 => get_string('statusexcused', 'mod_attendance')
            ];
            
            // Get user information
            $userids = [];
            $studentids = [];
            
            foreach ($auditlogs as $log) {
                $userids[] = $log->userid;
                $studentids[] = $log->studentid;
            }
            
            // Get users
            $users = [];
            if (!empty($userids)) {
                list($usql, $uparams) = $DB->get_in_or_equal(array_unique($userids));
                $users = $DB->get_records_select('user', "id $usql", $uparams, '', 'id, firstname, lastname');
            }
            
            // Get students
            $students = [];
            if (!empty($studentids)) {
                list($ssql, $sparams) = $DB->get_in_or_equal(array_unique($studentids));
                $students = $DB->get_records_select('user', "id $ssql", $sparams, '', 'id, firstname, lastname');
            }
            
            // Get sessions
            $sessions = $attendanceinstance->get_sessions();
            
            foreach ($auditlogs as $log) {
                // Status values
                $statusbefore = isset($log->statusbefore) ? 
                    (isset($statusoptions[$log->statusbefore]) ? $statusoptions[$log->statusbefore] : get_string('unknown', 'mod_attendance')) : 
                    get_string('none');
                    
                $statusafter = isset($statusoptions[$log->statusafter]) ? 
                    $statusoptions[$log->statusafter] : 
                    get_string('unknown', 'mod_attendance');
                
                // Format remarks, being careful with potentially long content
                $remarksbefore = isset($log->remarksbefore) ? s(core_text::substr($log->remarksbefore, 0, 30)) : '';
                if (isset($log->remarksbefore) && core_text::strlen($log->remarksbefore) > 30) {
                    $remarksbefore .= '...';
                }
                
                $remarksafter = isset($log->remarksafter) ? s(core_text::substr($log->remarksafter, 0, 30)) : '';
                if (isset($log->remarksafter) && core_text::strlen($log->remarksafter) > 30) {
                    $remarksafter .= '...';
                }
                
                // User who made the change
                $user = isset($users[$log->userid]) ? fullname($users[$log->userid]) : get_string('unknown', 'mod_attendance');
                
                // Student whose attendance was changed
                $student = isset($students[$log->studentid]) ? fullname($students[$log->studentid]) : get_string('unknown', 'mod_attendance');
                
                // Session date
                $sessiondate = isset($sessions[$log->sessionid]) ? 
                    userdate($sessions[$log->sessionid]->sessiondate) : 
                    get_string('unknown', 'mod_attendance');
                
                $table->data[] = [
                    userdate($log->timecreated),
                    $sessiondate,
                    $student,
                    $user,
                    $statusbefore,
                    $statusafter,
                    $remarksbefore,
                    $remarksafter,
                    s($log->ipaddress)
                ];
            }
            
            echo html_writer::table($table);
        }
        break;
}

echo $OUTPUT->footer(); 
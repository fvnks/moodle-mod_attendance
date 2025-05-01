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
 * Attendance class for managing attendance sessions and logs
 *
 * @package    mod_attendance
 * @copyright  2023 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_attendance;

defined('MOODLE_INTERNAL') || die();

/**
 * Class attendance
 *
 * @package   mod_attendance
 * @copyright 2023 Your Name
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class attendance {
    /** @var \stdClass The attendance instance record */
    private $instance;
    
    /** @var \context_module The module context */
    private $context;
    
    /** @var int Course module ID */
    private $cmid;
    
    /**
     * Constructor
     *
     * @param int $cmid The course module ID for this attendance instance
     */
    public function __construct($cmid) {
        global $DB;
        
        $this->cmid = $cmid;
        list($course, $cm) = get_course_and_cm_from_cmid($cmid, 'attendance');
        $this->context = \context_module::instance($cmid);
        $this->instance = $DB->get_record('attendance', ['id' => $cm->instance], '*', MUST_EXIST);
    }
    
    /**
     * Get the attendance instance
     *
     * @return \stdClass The attendance instance
     */
    public function get_instance() {
        return $this->instance;
    }
    
    /**
     * Get the context
     *
     * @return \context_module The module context
     */
    public function get_context() {
        return $this->context;
    }
    
    /**
     * Get all sessions for this attendance instance
     *
     * @return array Array of session records
     */
    public function get_sessions() {
        global $DB;
        
        return $DB->get_records('attendance_sessions', ['attendanceid' => $this->instance->id], 'sessiondate ASC');
    }
    
    /**
     * Get a specific session
     *
     * @param int $sessionid The session ID
     * @return \stdClass|false The session record or false if not found
     */
    public function get_session($sessionid) {
        global $DB;
        
        return $DB->get_record('attendance_sessions', ['id' => $sessionid, 'attendanceid' => $this->instance->id]);
    }
    
    /**
     * Add a new session
     *
     * @param \stdClass $data The session data
     * @return int|false The new session ID or false on failure
     */
    public function add_session($data) {
        global $DB, $USER;
        
        // Sanitize and validate input data
        $session = new \stdClass();
        $session->attendanceid = $this->instance->id;
        $session->description = clean_param($data->description, PARAM_TEXT);
        $session->sessiondate = clean_param($data->sessiondate, PARAM_INT);
        $session->duration = clean_param($data->duration, PARAM_INT);
        $session->timecreated = time();
        $session->timemodified = time();
        
        // Additional validation
        if ($session->duration <= 0 || $session->duration > 1440) { // Max 24 hours in minutes
            return false;
        }
        
        if (empty($session->description)) {
            $session->description = '';
        }
        
        $sessionid = $DB->insert_record('attendance_sessions', $session);
        
        // Trigger session created event
        $event = \mod_attendance\event\session_created::create([
            'objectid' => $sessionid,
            'context' => $this->context,
            'other' => [
                'attendanceid' => $this->instance->id,
                'sessiondate' => $session->sessiondate
            ]
        ]);
        $event->trigger();
        
        return $sessionid;
    }
    
    /**
     * Update an existing session
     *
     * @param \stdClass $data The session data
     * @return bool True if successful
     */
    public function update_session($data) {
        global $DB;
        
        // Sanitize and validate input data
        $session = new \stdClass();
        $session->id = clean_param($data->id, PARAM_INT);
        $session->description = clean_param($data->description, PARAM_TEXT);
        $session->sessiondate = clean_param($data->sessiondate, PARAM_INT);
        $session->duration = clean_param($data->duration, PARAM_INT);
        $session->timemodified = time();
        
        // Additional validation
        if ($session->duration <= 0 || $session->duration > 1440) { // Max 24 hours in minutes
            return false;
        }
        
        // Verify the session belongs to this attendance instance
        if (!$this->get_session($session->id)) {
            return false;
        }
        
        $result = $DB->update_record('attendance_sessions', $session);
        
        // Trigger session updated event
        $event = \mod_attendance\event\session_updated::create([
            'objectid' => $session->id,
            'context' => $this->context,
            'other' => [
                'attendanceid' => $this->instance->id
            ]
        ]);
        $event->trigger();
        
        return $result;
    }
    
    /**
     * Delete a session
     *
     * @param int $sessionid The session ID
     * @return bool True if successful
     */
    public function delete_session($sessionid) {
        global $DB;
        
        // Check if session exists and belongs to this attendance instance
        $session = $this->get_session($sessionid);
        if (!$session) {
            return false;
        }
        
        // Delete all attendance logs for this session
        $DB->delete_records('attendance_log', ['sessionid' => $sessionid]);
        
        // Delete the session
        $result = $DB->delete_records('attendance_sessions', ['id' => $sessionid]);
        
        // Trigger session deleted event
        $event = \mod_attendance\event\session_deleted::create([
            'objectid' => $sessionid,
            'context' => $this->context,
            'other' => [
                'attendanceid' => $this->instance->id
            ]
        ]);
        $event->trigger();
        
        return $result;
    }
    
    /**
     * Take attendance for a session
     *
     * @param int $sessionid The session ID
     * @param array $attendancedata Array of student attendance data (studentid => status)
     * @param array $remarks Array of student remarks (studentid => remarks)
     * @return bool True if successful
     */
    public function take_attendance($sessionid, $attendancedata, $remarks = []) {
        global $DB, $USER, $CFG;
        
        // Clean and validate session ID
        $sessionid = clean_param($sessionid, PARAM_INT);
        
        // Check if session exists and belongs to this attendance instance
        $session = $this->get_session($sessionid);
        if (!$session) {
            return false;
        }
        
        // Check if session is locked
        if (!empty($session->locked)) {
            // Only users with change capability can modify locked sessions
            if (!has_capability('mod/attendance:changeattendances', $this->context)) {
                return false;
            }
        }
        
        // Begin transaction
        $transaction = $DB->start_delegated_transaction();
        
        try {
            // Get enrolled students to validate student IDs
            $validstudents = get_enrolled_users($this->context, 'mod/attendance:view', 0, 'u.id', null, 0, 0, true);
            $validstudentids = array_keys($validstudents);
            
            // Get current IP address for audit log
            $ipaddress = getremoteaddr();
            
            foreach ($attendancedata as $studentid => $status) {
                // Validate student ID and status
                $studentid = clean_param($studentid, PARAM_INT);
                $status = clean_param($status, PARAM_INT);
                
                // Verify this is a valid enrolled student
                if (!in_array($studentid, $validstudentids)) {
                    continue;
                }
                
                // Validate status value (0-3)
                if ($status < 0 || $status > 3) {
                    $status = 0; // Default to absent if invalid
                }
                
                // Clean and truncate remarks if present
                $remark = '';
                if (isset($remarks[$studentid])) {
                    $remark = clean_param($remarks[$studentid], PARAM_TEXT);
                    $remark = core_text::substr($remark, 0, 255); // Limit to 255 chars
                }
                
                // Check if record already exists
                $existingrecord = $DB->get_record('attendance_log', [
                    'sessionid' => $sessionid,
                    'studentid' => $studentid
                ]);
                
                // Create audit entry if updating existing record
                if ($existingrecord) {
                    // Only create audit entry if something changed
                    if ($existingrecord->status != $status || $existingrecord->remarks != $remark) {
                        $audit = new \stdClass();
                        $audit->sessionid = $sessionid;
                        $audit->studentid = $studentid;
                        $audit->statusbefore = $existingrecord->status;
                        $audit->statusafter = $status;
                        $audit->remarksbefore = $existingrecord->remarks;
                        $audit->remarksafter = $remark;
                        $audit->userid = $USER->id;
                        $audit->timecreated = time();
                        $audit->ipaddress = $ipaddress;
                        
                        $DB->insert_record('attendance_audit', $audit);
                    }
                    
                    // Update existing record
                    $record = new \stdClass();
                    $record->id = $existingrecord->id;
                    $record->status = $status;
                    $record->remarks = $remark;
                    $record->timemodified = time();
                    $record->taker = $USER->id;
                    
                    $DB->update_record('attendance_log', $record);
                } else {
                    // Create new record
                    $record = new \stdClass();
                    $record->sessionid = $sessionid;
                    $record->studentid = $studentid;
                    $record->status = $status;
                    $record->remarks = $remark;
                    $record->timecreated = time();
                    $record->timemodified = time();
                    $record->taker = $USER->id;
                    
                    $DB->insert_record('attendance_log', $record);
                    
                    // Create audit entry for new record
                    $audit = new \stdClass();
                    $audit->sessionid = $sessionid;
                    $audit->studentid = $studentid;
                    $audit->statusbefore = null;
                    $audit->statusafter = $status;
                    $audit->remarksbefore = null;
                    $audit->remarksafter = $remark;
                    $audit->userid = $USER->id;
                    $audit->timecreated = time();
                    $audit->ipaddress = $ipaddress;
                    
                    $DB->insert_record('attendance_audit', $audit);
                }
            }
            
            // Update session with last taken info
            $sessionupdate = new \stdClass();
            $sessionupdate->id = $sessionid;
            $sessionupdate->lasttaken = time();
            $sessionupdate->lasttakenby = $USER->id;
            $sessionupdate->timemodified = time();
            
            $DB->update_record('attendance_sessions', $sessionupdate);
            
            // Commit transaction
            $transaction->allow_commit();
            
            // Trigger attendance taken event
            $event = \mod_attendance\event\attendance_taken::create([
                'objectid' => $sessionid,
                'context' => $this->context,
                'other' => [
                    'attendanceid' => $this->instance->id
                ]
            ]);
            $event->trigger();
            
            return true;
        } catch (\Exception $e) {
            // Rollback transaction in case of error
            $transaction->rollback($e);
            return false;
        }
    }
    
    /**
     * Lock a session to prevent further changes
     *
     * @param int $sessionid The session ID
     * @return bool True if successful
     */
    public function lock_session($sessionid) {
        global $DB;
        
        // Clean and validate session ID
        $sessionid = clean_param($sessionid, PARAM_INT);
        
        // Check if session exists and belongs to this attendance instance
        $session = $this->get_session($sessionid);
        if (!$session) {
            return false;
        }
        
        // Check permission
        if (!has_capability('mod/attendance:managesessions', $this->context)) {
            return false;
        }
        
        // Update session to locked
        $sessionupdate = new \stdClass();
        $sessionupdate->id = $sessionid;
        $sessionupdate->locked = 1;
        $sessionupdate->timemodified = time();
        
        return $DB->update_record('attendance_sessions', $sessionupdate);
    }
    
    /**
     * Unlock a session to allow changes
     *
     * @param int $sessionid The session ID
     * @return bool True if successful
     */
    public function unlock_session($sessionid) {
        global $DB;
        
        // Clean and validate session ID
        $sessionid = clean_param($sessionid, PARAM_INT);
        
        // Check if session exists and belongs to this attendance instance
        $session = $this->get_session($sessionid);
        if (!$session) {
            return false;
        }
        
        // Check permission - requires higher permission than just manage
        if (!has_capability('mod/attendance:changeattendances', $this->context)) {
            return false;
        }
        
        // Update session to unlocked
        $sessionupdate = new \stdClass();
        $sessionupdate->id = $sessionid;
        $sessionupdate->locked = 0;
        $sessionupdate->timemodified = time();
        
        return $DB->update_record('attendance_sessions', $sessionupdate);
    }
    
    /**
     * Get attendance audit log for a session
     *
     * @param int $sessionid The session ID
     * @return array Array of audit log records
     */
    public function get_session_audit_logs($sessionid) {
        global $DB;
        
        // Clean and validate session ID
        $sessionid = clean_param($sessionid, PARAM_INT);
        
        // Check if session exists and belongs to this attendance instance
        $session = $this->get_session($sessionid);
        if (!$session) {
            return [];
        }
        
        // Check permission
        if (!has_capability('mod/attendance:viewreports', $this->context)) {
            return [];
        }
        
        return $DB->get_records('attendance_audit', ['sessionid' => $sessionid], 'timecreated DESC');
    }
    
    /**
     * Get attendance log for a session
     *
     * @param int $sessionid The session ID
     * @return array Array of attendance log records
     */
    public function get_session_logs($sessionid) {
        global $DB;
        
        // Check if session exists and belongs to this attendance instance
        $session = $this->get_session($sessionid);
        if (!$session) {
            return [];
        }
        
        return $DB->get_records('attendance_log', ['sessionid' => $sessionid]);
    }
    
    /**
     * Get attendance summary for a student
     *
     * @param int $studentid The student ID
     * @return \stdClass Object with attendance summary
     */
    public function get_student_summary($studentid) {
        global $DB;
        
        $summary = new \stdClass();
        $summary->total = 0;
        $summary->present = 0;
        $summary->absent = 0;
        $summary->late = 0;
        $summary->excused = 0;
        
        // Get all sessions for this attendance instance
        $sessions = $this->get_sessions();
        $summary->total = count($sessions);
        
        if (empty($sessions)) {
            return $summary;
        }
        
        $sessionids = array_keys($sessions);
        
        // Get the student's attendance logs
        list($insql, $params) = $DB->get_in_or_equal($sessionids);
        $params[] = $studentid;
        
        $sql = "SELECT al.* 
                FROM {attendance_log} al
                WHERE al.sessionid $insql AND al.studentid = ?";
        
        $logs = $DB->get_records_sql($sql, $params);
        
        // Count statuses
        foreach ($logs as $log) {
            switch ($log->status) {
                case 1: // Present
                    $summary->present++;
                    break;
                case 0: // Absent
                    $summary->absent++;
                    break;
                case 2: // Late
                    $summary->late++;
                    break;
                case 3: // Excused
                    $summary->excused++;
                    break;
            }
        }
        
        return $summary;
    }
    
    /**
     * Get attendance summary for all students in a course
     *
     * @return array Array of student attendance summaries
     */
    public function get_course_summary() {
        global $DB;
        
        // Get enrolled students
        $enrolled = get_enrolled_users($this->context, 'mod/attendance:view', 0, 'u.id, u.firstname, u.lastname, u.email');
        
        if (empty($enrolled)) {
            return [];
        }
        
        $summary = [];
        
        foreach ($enrolled as $student) {
            $summary[$student->id] = new \stdClass();
            $summary[$student->id]->id = $student->id;
            $summary[$student->id]->firstname = $student->firstname;
            $summary[$student->id]->lastname = $student->lastname;
            $summary[$student->id]->email = $student->email;
            
            // Get attendance summary for this student
            $studentSummary = $this->get_student_summary($student->id);
            
            $summary[$student->id]->total = $studentSummary->total;
            $summary[$student->id]->present = $studentSummary->present;
            $summary[$student->id]->absent = $studentSummary->absent;
            $summary[$student->id]->late = $studentSummary->late;
            $summary[$student->id]->excused = $studentSummary->excused;
            
            // Calculate percentages
            if ($studentSummary->total > 0) {
                $summary[$student->id]->presentpercent = round(($studentSummary->present / $studentSummary->total) * 100);
                $summary[$student->id]->absentpercent = round(($studentSummary->absent / $studentSummary->total) * 100);
                $summary[$student->id]->latepercent = round(($studentSummary->late / $studentSummary->total) * 100);
                $summary[$student->id]->excusedpercent = round(($studentSummary->excused / $studentSummary->total) * 100);
            } else {
                $summary[$student->id]->presentpercent = 0;
                $summary[$student->id]->absentpercent = 0;
                $summary[$student->id]->latepercent = 0;
                $summary[$student->id]->excusedpercent = 0;
            }
        }
        
        return $summary;
    }
} 
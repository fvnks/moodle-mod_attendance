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
 * Library of functions for the attendance module
 *
 * @package    mod_attendance
 * @copyright  2023 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Return if the plugin supports $feature.
 *
 * @param string $feature Constant representing the feature.
 * @return true | null True if the feature is supported, null otherwise.
 */
function attendance_supports($feature) {
    switch ($feature) {
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_GRADE_HAS_GRADE:
            return true;
        case FEATURE_GROUPS:
            return true;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        case FEATURE_SHOW_DESCRIPTION:
            return true;
        default:
            return null;
    }
}

/**
 * Adds a new attendance instance
 *
 * @param stdClass $attendance The attendance instance data
 * @return int The instance id
 */
function attendance_add_instance($attendance) {
    global $DB;
    
    $attendance->timecreated = time();
    $attendance->timemodified = time();
    
    $id = $DB->insert_record('attendance', $attendance);
    
    return $id;
}

/**
 * Updates an existing attendance instance
 *
 * @param stdClass $attendance The attendance instance data
 * @return bool True if successful
 */
function attendance_update_instance($attendance) {
    global $DB;
    
    $attendance->timemodified = time();
    $attendance->id = $attendance->instance;
    
    return $DB->update_record('attendance', $attendance);
}

/**
 * Deletes an attendance instance
 *
 * @param int $id The attendance instance ID
 * @return bool True if successful
 */
function attendance_delete_instance($id) {
    global $DB;
    
    if (!$attendance = $DB->get_record('attendance', ['id' => $id])) {
        return false;
    }
    
    // Delete all associated logs first
    $DB->delete_records_select(
        'attendance_log',
        "sessionid IN (SELECT id FROM {attendance_sessions} WHERE attendanceid = :attendanceid)",
        ['attendanceid' => $id]
    );
    
    // Delete all sessions
    $DB->delete_records('attendance_sessions', ['attendanceid' => $id]);
    
    // Delete the attendance instance
    $DB->delete_records('attendance', ['id' => $id]);
    
    return true;
}

/**
 * Returns the information on whether the module supports a feature
 *
 * @see plugin_supports() in lib/moodlelib.php
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, false if not, null if doesn't know
 */
function attendance_get_completion_state($course, $cm, $userid, $type) {
    global $DB;
    
    // Get the attendance instance
    if (!$attendance = $DB->get_record('attendance', ['id' => $cm->instance])) {
        return false;
    }
    
    // Get all sessions for this attendance activity
    $sessions = $DB->get_records('attendance_sessions', ['attendanceid' => $attendance->id]);
    if (empty($sessions)) {
        return false;
    }
    
    $sessionids = array_keys($sessions);
    
    // Get the student's attendance logs
    list($insql, $params) = $DB->get_in_or_equal($sessionids);
    $params[] = $userid;
    $sql = "SELECT COUNT(id) FROM {attendance_log} 
            WHERE sessionid $insql AND studentid = ?";
    $attended = $DB->count_records_sql($sql, $params);
    
    // Get the total number of sessions
    $totalsessions = count($sessions);
    
    // Calculate attendance rate
    $attendancerate = ($totalsessions > 0) ? ($attended / $totalsessions) * 100 : 0;
    
    // Completion is achieved when attendance rate meets or exceeds the required rate
    // Currently set to 75% as an example
    return ($attendancerate >= 75);
} 
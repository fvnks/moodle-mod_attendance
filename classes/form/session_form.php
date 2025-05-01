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
 * Session form for the attendance module
 *
 * @package    mod_attendance
 * @copyright  2023 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir.'/formslib.php');

/**
 * Session form class for the attendance module
 *
 * @package   mod_attendance
 * @copyright 2023 Your Name
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_attendance_session_form extends moodleform {
    /**
     * Define the form.
     */
    public function definition() {
        global $CFG;
        
        $mform = $this->_form;
        $customdata = $this->_customdata;
        $cm = $customdata['cm'];
        $session = $customdata['session'];
        
        // Hidden fields
        $mform->addElement('hidden', 'id', $cm->id);
        $mform->setType('id', PARAM_INT);
        
        $mform->addElement('hidden', 'action');
        $mform->setType('action', PARAM_ALPHA);
        
        if ($session && isset($session->id)) {
            $mform->addElement('hidden', 'sessionid', $session->id);
            $mform->setType('sessionid', PARAM_INT);
        }
        
        // Session description
        $mform->addElement('text', 'description', get_string('sessiondescription', 'mod_attendance'), ['size' => '64']);
        $mform->setType('description', PARAM_TEXT);
        $mform->addRule('description', null, 'required', null, 'client');
        
        // Session date
        $mform->addElement('date_time_selector', 'sessiondate', get_string('sessiondate', 'mod_attendance'));
        $mform->setType('sessiondate', PARAM_INT);
        $mform->addRule('sessiondate', null, 'required', null, 'client');
        
        // Duration (in minutes)
        $mform->addElement('text', 'duration', get_string('sessionduration', 'mod_attendance'), ['size' => '5']);
        $mform->setType('duration', PARAM_INT);
        $mform->setDefault('duration', 60); // Default to 1 hour
        $mform->addRule('duration', null, 'required', null, 'client');
        $mform->addRule('duration', get_string('numericalvalue', 'error'), 'numeric', null, 'client');
        
        // Fill form with existing data if editing
        if ($session) {
            $this->set_data($session);
        } else {
            // Set some defaults
            $mform->setDefault('action', 'add');
        }
        
        // Add standard buttons
        $this->add_action_buttons();
    }
    
    /**
     * Validate the form data.
     *
     * @param array $data The data submitted
     * @param array $files Files included in the form submission
     * @return array Validation errors
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        
        // Validate the duration is a positive integer
        if (isset($data['duration']) && ($data['duration'] <= 0 || !is_numeric($data['duration']))) {
            $errors['duration'] = get_string('durationmustbepositive', 'mod_attendance');
        }
        
        // We don't validate session dates to be in the future
        // This allows for backdating sessions if needed
        
        return $errors;
    }
} 
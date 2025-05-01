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
 * Version information for the attendance plugin
 *
 * @package    mod_attendance
 * @copyright  2023 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version = 2023110405;  // YYYYMMDDXX (year, month, day, 24-hour time)
$plugin->requires = 2022112800; // Moodle 4.1 requirement
$plugin->component = 'mod_attendance';
$plugin->maturity = MATURITY_ALPHA;
$plugin->release = '0.1.8';
$plugin->supported_languages = array('en', 'es_cl'); // Idiomas soportados 
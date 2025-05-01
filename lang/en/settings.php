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
 * Language strings for the attendance module settings
 *
 * @package    mod_attendance
 * @copyright  2023 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

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
$string['downloadupdate'] = 'Download update';
$string['releasenotes'] = 'Release notes'; 
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
 * Script para comprobar actualizaciones del plugin desde GitHub
 *
 * @package    mod_attendance
 * @copyright  2023 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Asegurarse de incluir la configuración global de Moodle
require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir.'/adminlib.php');

// Verificar permisos
require_login();
$context = context_system::instance();
require_capability('moodle/site:config', $context);

// Configuración de la página
admin_externalpage_setup('modsettingattendance');

// Obtener la configuración del repositorio
$owner = get_config('mod_attendance', 'githubusuario');
$repo = get_config('mod_attendance', 'githubrepo');

// Si no hay configuración, redirigir con error
if (empty($owner) || empty($repo)) {
    redirect(
        new moodle_url('/admin/settings.php', ['section' => 'modsettingattendance']),
        get_string('updatecheckerror', 'mod_attendance'),
        null,
        \core\output\notification::NOTIFY_ERROR
    );
}

// Verificar actualizaciones
$updater = new \mod_attendance\github_updater($owner, $repo);
$updateinfo = $updater->check_for_updates();

// Preparar página
$PAGE->set_url(new moodle_url('/mod/attendance/check_updates.php'));
$PAGE->set_title(get_string('checkupdates', 'mod_attendance'));
$PAGE->set_heading(get_string('checkupdates', 'mod_attendance'));

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('checkupdates', 'mod_attendance'));

// Mostrar resultados
if ($updateinfo) {
    // Hay actualizaciones disponibles
    $message = get_string('updateavailable', 'mod_attendance', $updateinfo);
    $notification = new \core\output\notification(
        $message,
        \core\output\notification::NOTIFY_INFO
    );
    echo $OUTPUT->render($notification);
    
    // Mostrar notas de la versión si están disponibles
    if (!empty($updateinfo->release_notes)) {
        echo $OUTPUT->heading(get_string('releasenotes', 'core_admin'), 3);
        echo html_writer::tag('div', format_text($updateinfo->release_notes, FORMAT_MARKDOWN), ['class' => 'release-notes']);
    }
    
    // Enlace a la descarga
    echo html_writer::div(
        html_writer::link(
            $updateinfo->download_url,
            get_string('downloadupdate', 'core_admin'),
            ['class' => 'btn btn-primary', 'target' => '_blank']
        ),
        'mt-3'
    );
    
} else {
    // No hay actualizaciones disponibles
    $notification = new \core\output\notification(
        get_string('noupdatesavailable', 'mod_attendance'),
        \core\output\notification::NOTIFY_SUCCESS
    );
    echo $OUTPUT->render($notification);
}

// Enlace para reportar problemas
$issueurl = $updater->get_issue_report_url();
echo html_writer::div(
    html_writer::link(
        $issueurl,
        get_string('reportissue', 'mod_attendance'),
        ['class' => 'btn btn-secondary', 'target' => '_blank']
    ),
    'mt-3'
);

// Enlace para volver a la configuración
$settingsurl = new moodle_url('/admin/settings.php', ['section' => 'modsettingattendance']);
echo html_writer::div(
    html_writer::link(
        $settingsurl,
        get_string('back', 'mod_attendance'),
        ['class' => 'btn btn-link']
    ),
    'mt-3'
);

echo $OUTPUT->footer(); 
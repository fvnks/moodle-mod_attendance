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
 * Configuración del módulo de asistencia
 *
 * @package    mod_attendance
 * @copyright  2023 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    // Encabezado para las configuraciones
    $settings->add(new admin_setting_heading(
        'mod_attendance_general',
        get_string('generalsettings', 'mod_attendance'),
        get_string('configintro', 'mod_attendance')
    ));
    
    // Porcentaje mínimo de asistencia requerido (relevante para Chile)
    $settings->add(new admin_setting_configtext(
        'mod_attendance/minimaporcentajeasis',
        get_string('minimumattendance', 'mod_attendance'),
        get_string('minimumattendance_desc', 'mod_attendance'),
        '85', // Valor predeterminado común en Chile
        PARAM_INT
    ));
    
    // Configuración de bloqueo automático de sesiones pasadas
    $settings->add(new admin_setting_configcheckbox(
        'mod_attendance/bloqueoauto',
        get_string('autolock', 'mod_attendance'),
        get_string('autolock_desc', 'mod_attendance'),
        1 // Habilitado por defecto
    ));
    
    // Días después de los cuales una sesión se bloquea automáticamente
    $settings->add(new admin_setting_configtext(
        'mod_attendance/diasbloqueoauto',
        get_string('autolockdays', 'mod_attendance'),
        get_string('autolockdays_desc', 'mod_attendance'),
        '7', // 7 días por defecto
        PARAM_INT
    ));
    
    // Estados de asistencia - Configuración de cuáles están habilitados
    $settings->add(new admin_setting_heading(
        'mod_attendance_statusconfig',
        get_string('statusconfig', 'mod_attendance'),
        get_string('statusconfig_desc', 'mod_attendance')
    ));
    
    // Configuración para cada estado
    $estados = array(
        'presente' => get_string('statuspresent', 'mod_attendance'),
        'ausente' => get_string('statusabsent', 'mod_attendance'),
        'atrasado' => get_string('statuslate', 'mod_attendance'),
        'justificado' => get_string('statusexcused', 'mod_attendance')
    );
    
    foreach ($estados as $key => $estado) {
        $settings->add(new admin_setting_configcheckbox(
            'mod_attendance/habilitar_' . $key,
            get_string('enable', 'mod_attendance') . ' ' . $estado,
            get_string('enablestatus_desc', 'mod_attendance', $estado),
            1 // Todos habilitados por defecto
        ));
    }
    
    // Puntos para cada estado
    $settings->add(new admin_setting_configtext(
        'mod_attendance/puntos_presente',
        get_string('points', 'mod_attendance') . ' ' . get_string('statuspresent', 'mod_attendance'),
        get_string('points_desc', 'mod_attendance', get_string('statuspresent', 'mod_attendance')),
        '1', // 1 punto completo
        PARAM_FLOAT
    ));
    
    $settings->add(new admin_setting_configtext(
        'mod_attendance/puntos_ausente',
        get_string('points', 'mod_attendance') . ' ' . get_string('statusabsent', 'mod_attendance'),
        get_string('points_desc', 'mod_attendance', get_string('statusabsent', 'mod_attendance')),
        '0', // 0 puntos
        PARAM_FLOAT
    ));
    
    $settings->add(new admin_setting_configtext(
        'mod_attendance/puntos_atrasado',
        get_string('points', 'mod_attendance') . ' ' . get_string('statuslate', 'mod_attendance'),
        get_string('points_desc', 'mod_attendance', get_string('statuslate', 'mod_attendance')),
        '0.5', // 0.5 puntos
        PARAM_FLOAT
    ));
    
    $settings->add(new admin_setting_configtext(
        'mod_attendance/puntos_justificado',
        get_string('points', 'mod_attendance') . ' ' . get_string('statusexcused', 'mod_attendance'),
        get_string('points_desc', 'mod_attendance', get_string('statusexcused', 'mod_attendance')),
        '0.7', // 0.7 puntos (se considera algo en el sistema chileno)
        PARAM_FLOAT
    ));
    
    // Configuración para informes
    $settings->add(new admin_setting_heading(
        'mod_attendance_reports',
        get_string('reportsettings', 'mod_attendance'),
        get_string('reportsettings_desc', 'mod_attendance')
    ));
    
    // Mostrar alerta para estudiantes con baja asistencia
    $settings->add(new admin_setting_configcheckbox(
        'mod_attendance/alertaasistencia',
        get_string('attendancealert', 'mod_attendance'),
        get_string('attendancealert_desc', 'mod_attendance'),
        1 // Habilitado por defecto
    ));
    
    // Color para estudiantes con baja asistencia
    $settings->add(new admin_setting_configcolourpicker(
        'mod_attendance/coloralerta',
        get_string('alertcolor', 'mod_attendance'),
        get_string('alertcolor_desc', 'mod_attendance'),
        '#EB3B3B' // Rojo por defecto
    ));
    
    // Configuración de actualización desde GitHub
    $settings->add(new admin_setting_heading(
        'mod_attendance_github',
        get_string('githubupdatesettings', 'mod_attendance'),
        get_string('githubupdatesettings_desc', 'mod_attendance')
    ));
    
    // Habilitar comprobación de actualizaciones
    $settings->add(new admin_setting_configcheckbox(
        'mod_attendance/habilitaractualizacion',
        get_string('enableupdatecheck', 'mod_attendance'),
        get_string('enableupdatecheck_desc', 'mod_attendance'),
        1 // Habilitado por defecto
    ));
    
    // Propietario del repositorio
    $settings->add(new admin_setting_configtext(
        'mod_attendance/githubusuario',
        get_string('githubowner', 'mod_attendance'),
        get_string('githubowner_desc', 'mod_attendance'),
        '', // Sin valor predeterminado
        PARAM_TEXT
    ));
    
    // Nombre del repositorio
    $settings->add(new admin_setting_configtext(
        'mod_attendance/githubrepo',
        get_string('githubrepo', 'mod_attendance'),
        get_string('githubrepo_desc', 'mod_attendance'),
        '', // Sin valor predeterminado
        PARAM_TEXT
    ));
    
    // Frecuencia de comprobación de actualizaciones
    $options = [
        'daily' => get_string('daily', 'mod_attendance'),
        'weekly' => get_string('weekly', 'mod_attendance'),
        'monthly' => get_string('monthly', 'mod_attendance')
    ];
    
    $settings->add(new admin_setting_configselect(
        'mod_attendance/actualizacionfrecuencia',
        get_string('updatecheckfrequency', 'mod_attendance'),
        get_string('updatecheckfrequency_desc', 'mod_attendance'),
        'weekly', // Predeterminado semanal
        $options
    ));
    
    // Botón para comprobar actualizaciones ahora
    $checkupdateurl = new moodle_url('/mod/attendance/check_updates.php', array('sesskey' => sesskey()));
    $checkupdatebutton = html_writer::link(
        $checkupdateurl,
        get_string('checkupdatesnow', 'mod_attendance'),
        ['class' => 'btn btn-secondary']
    );
    
    $settings->add(new admin_setting_heading(
        'mod_attendance_checkupdates',
        get_string('checkupdates', 'mod_attendance'),
        get_string('checkupdates_desc', 'mod_attendance') . '<br /><br />' . $checkupdatebutton
    ));
} 
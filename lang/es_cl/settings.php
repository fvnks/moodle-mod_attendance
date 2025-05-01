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
 * Archivo de idioma para español de Chile del módulo de asistencia (configuraciones)
 *
 * @package    mod_attendance
 * @copyright  2023 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Settings page
$string['generalsettings'] = 'Configuración general';
$string['configintro'] = 'Estas configuraciones permiten personalizar el comportamiento general del módulo de asistencia.';
$string['minimumattendance'] = 'Porcentaje mínimo de asistencia';
$string['minimumattendance_desc'] = 'Porcentaje mínimo de asistencia requerido para aprobar el curso (valor usado en reportes y alertas).';
$string['autolock'] = 'Bloqueo automático de sesiones';
$string['autolock_desc'] = 'Si está habilitado, las sesiones se bloquearán automáticamente después de cierto tiempo.';
$string['autolockdays'] = 'Días para bloqueo automático';
$string['autolockdays_desc'] = 'Número de días después de la fecha de la sesión para bloquearla automáticamente.';

$string['statusconfig'] = 'Configuración de estados de asistencia';
$string['statusconfig_desc'] = 'Aquí puede configurar los estados disponibles y sus valores.';
$string['enable'] = 'Habilitar';
$string['enablestatus_desc'] = 'Marque para habilitar el estado de asistencia "{$a}".';
$string['points'] = 'Puntos para';
$string['points_desc'] = 'Valor de puntuación para el estado "{$a}".';

$string['reportsettings'] = 'Configuración de informes';
$string['reportsettings_desc'] = 'Configure cómo se muestran los informes de asistencia.';
$string['attendancealert'] = 'Alerta de baja asistencia';
$string['attendancealert_desc'] = 'Resaltar estudiantes con asistencia por debajo del porcentaje mínimo configurado.';
$string['alertcolor'] = 'Color de alerta';
$string['alertcolor_desc'] = 'Color para resaltar estudiantes con baja asistencia.';

$string['githubupdatesettings'] = 'Configuración de actualizaciones desde GitHub';
$string['githubupdatesettings_desc'] = 'Configure cómo el plugin se actualiza desde un repositorio de GitHub.';
$string['enableupdatecheck'] = 'Habilitar comprobación de actualizaciones';
$string['enableupdatecheck_desc'] = 'Si está habilitado, el plugin comprobará periódicamente si hay actualizaciones disponibles en GitHub.';
$string['githubowner'] = 'Propietario del repositorio en GitHub';
$string['githubowner_desc'] = 'Nombre de usuario o organización que es propietaria del repositorio (por ejemplo: "usuario").';
$string['githubrepo'] = 'Nombre del repositorio';
$string['githubrepo_desc'] = 'Nombre del repositorio en GitHub donde se aloja el plugin (por ejemplo: "moodle-mod_attendance").';
$string['updatecheckfrequency'] = 'Frecuencia de comprobación';
$string['updatecheckfrequency_desc'] = 'Con qué frecuencia se debe comprobar si hay actualizaciones disponibles.';
$string['daily'] = 'Diariamente';
$string['weekly'] = 'Semanalmente';
$string['monthly'] = 'Mensualmente';
$string['checkupdates'] = 'Comprobar actualizaciones';
$string['checkupdates_desc'] = 'Comprueba manualmente si hay actualizaciones disponibles para el plugin.';
$string['checkupdatesnow'] = 'Comprobar actualizaciones ahora';
$string['updateavailable'] = 'Hay una nueva versión disponible ({$a->latest_version}) del plugin de asistencia. Actualmente estás usando la versión {$a->current_version}. <a href="{$a->download_url}" target="_blank">Descargar actualización</a>';
$string['noupdatesavailable'] = 'Ya estás utilizando la última versión del plugin de asistencia.';
$string['updatecheckerror'] = 'No se pudo comprobar si hay actualizaciones disponibles. Verifica la configuración del repositorio.';
$string['downloadupdate'] = 'Descargar actualización';
$string['releasenotes'] = 'Notas de la versión'; 
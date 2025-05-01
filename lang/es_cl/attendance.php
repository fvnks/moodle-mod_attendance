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
 * Archivo de idioma para español de Chile del módulo de asistencia
 *
 * @package    mod_attendance
 * @copyright  2023 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['modulename'] = 'Asistencia';
$string['modulenameplural'] = 'Asistencias';
$string['modulename_help'] = 'El módulo de actividad de asistencia permite a los profesores registrar la asistencia en clases y a los estudiantes consultar su registro de asistencia.';
$string['pluginname'] = 'Asistencia';
$string['pluginadministration'] = 'Administración de asistencia';

$string['attendance'] = 'Asistencia';
$string['attendance:addinstance'] = 'Agregar una nueva actividad de asistencia';
$string['attendance:managesessions'] = 'Administrar sesiones de asistencia';
$string['attendance:takeattendances'] = 'Tomar asistencia';
$string['attendance:view'] = 'Ver información de asistencia';
$string['attendance:viewown'] = 'Ver registros de asistencia propios';
$string['attendance:viewreports'] = 'Ver informes de asistencia';
$string['attendance:changeattendances'] = 'Modificar registros de asistencia existentes';

$string['attendanceforthecourse'] = 'Asistencia para {$a}';
$string['addsession'] = 'Agregar sesión';
$string['editsession'] = 'Editar sesión';
$string['deletesession'] = 'Eliminar sesión';
$string['confirmdeletesession'] = '¿Estás seguro que deseas eliminar esta sesión?';

$string['sessiondate'] = 'Fecha de sesión';
$string['sessiondescription'] = 'Descripción';
$string['sessionduration'] = 'Duración (minutos)';
$string['sessions'] = 'Sesiones';
$string['sessionslist'] = 'Lista de sesiones';
$string['nosessionsfound'] = 'No se encontraron sesiones';
$string['sessionsaved'] = 'Sesión guardada exitosamente';
$string['sessiondeleted'] = 'Sesión eliminada exitosamente';
$string['sessiondateinpast'] = 'La fecha de la sesión no puede estar en el pasado';
$string['durationmustbepositive'] = 'La duración debe ser un número positivo';

$string['attendancestatus'] = 'Estado de asistencia';
$string['status'] = 'Estado';
$string['statuspresent'] = 'Presente';
$string['statusabsent'] = 'Ausente';
$string['statuslate'] = 'Atrasado';
$string['statusexcused'] = 'Justificado';
$string['remarks'] = 'Observaciones';

$string['attendancerecords'] = 'Registros de asistencia';
$string['noattendancerecords'] = 'No se encontraron registros de asistencia';
$string['takeattendance'] = 'Tomar asistencia';
$string['saveattendance'] = 'Guardar asistencia';
$string['attendancereport'] = 'Informe de asistencia';
$string['yourattendance'] = 'Tu asistencia';
$string['summary'] = 'Resumen';

$string['attendancesuccess'] = 'La asistencia ha sido registrada exitosamente';
$string['attendancesavefail'] = 'Error al guardar los datos de asistencia';

$string['student'] = 'Estudiante';
$string['studentsenrolled'] = 'Estudiantes matriculados';
$string['nostudentsenrolled'] = 'No hay estudiantes matriculados';
$string['presentpercentage'] = 'Presente (%)';
$string['absentpercentage'] = 'Ausente (%)';
$string['latepercentage'] = 'Atrasado (%)';
$string['excusedpercentage'] = 'Justificado (%)';
$string['totalsessions'] = 'Total de sesiones';
$string['unknown'] = 'Desconocido';
$string['back'] = 'Volver';

$string['eventsessioncreated'] = 'Sesión creada';
$string['eventsessionupdated'] = 'Sesión actualizada';
$string['eventsessiondeleted'] = 'Sesión eliminada';
$string['eventattendancetaken'] = 'Asistencia tomada';

// Session locking
$string['locked'] = 'Bloqueada';
$string['unlocked'] = 'Desbloqueada';
$string['modifiable'] = 'Modificable';
$string['locksession'] = 'Bloquear';
$string['unlocksession'] = 'Desbloquear';
$string['locksessionhelp'] = 'Bloquear esta sesión para evitar cambios por parte de profesores regulares';
$string['unlocksessionhelp'] = 'Desbloquear esta sesión para permitir cambios';
$string['sessionlocked'] = 'La sesión ha sido bloqueada exitosamente';
$string['sessionunlocked'] = 'La sesión ha sido desbloqueada exitosamente';
$string['sessionlockfailed'] = 'Error al bloquear la sesión';
$string['sessionunlockfailed'] = 'Error al desbloquear la sesión';
$string['sessionislocked'] = 'Esta sesión está bloqueada y no puede ser modificada';

// Audit log
$string['auditlog'] = 'Registro de auditoría';
$string['noauditlogs'] = 'No se encontraron registros de auditoría';
$string['viewaudit'] = 'Ver auditoría';
$string['date'] = 'Fecha';
$string['statusbefore'] = 'Estado anterior';
$string['statusafter'] = 'Nuevo estado';
$string['remarksbefore'] = 'Observaciones anteriores';
$string['remarksafter'] = 'Nuevas observaciones';
$string['ipaddress'] = 'Dirección IP';
$string['filtersession'] = 'Filtrar por sesión';
$string['allsessions'] = 'Todas las sesiones';
$string['filter'] = 'Filtrar';
$string['none'] = 'Ninguno';

// Input validation
$string['remarksvalidationmaxlength'] = 'Las observaciones no pueden exceder los 255 caracteres';
$string['remarksvalidationnohtml'] = 'No se permite HTML en las observaciones';
$string['nopermission'] = 'No tienes permiso para ver esta página';
$string['maxlengthexceeded'] = 'Se ha excedido la longitud máxima';
$string['inputcontainsjavascript'] = 'El texto ingresado contiene código JavaScript potencialmente peligroso';
$string['inputcontainshtml'] = 'El texto ingresado contiene código HTML potencialmente peligroso';
$string['invalidstatus'] = 'Estado de asistencia inválido';
$string['invalidstudent'] = 'Estudiante inválido';

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

// Scheduled task
$string['autolockscheduledtask'] = 'Bloqueo automático de sesiones de asistencia';
$string['checkupdatesscheduledtask'] = 'Comprobación automática de actualizaciones desde GitHub';

// GitHub update settings
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
$string['issuetracker'] = 'Reportar un problema';
$string['issuetracker_desc'] = 'Si encuentras un problema con el plugin, puedes reportarlo en el seguimiento de problemas de GitHub.';
$string['reportissue'] = 'Reportar un problema en GitHub'; 
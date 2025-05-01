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
 * Tarea programada para bloquear automáticamente sesiones antiguas.
 *
 * @package    mod_attendance
 * @copyright  2023 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_attendance\task;

defined('MOODLE_INTERNAL') || die();

/**
 * Tarea programada para bloquear automáticamente sesiones antiguas de asistencia.
 */
class autolock_sessions extends \core\task\scheduled_task {

    /**
     * Obtiene el nombre de la tarea.
     *
     * @return string Nombre descriptivo de la tarea
     */
    public function get_name() {
        return get_string('autolockscheduledtask', 'mod_attendance');
    }

    /**
     * Ejecuta la tarea de bloqueo automático.
     */
    public function execute() {
        mtrace('Iniciando tarea de bloqueo automático de sesiones de asistencia...');
        
        $config = new \mod_attendance\config();
        $count = $config::autolock_old_sessions();
        
        if ($count > 0) {
            mtrace("Se han bloqueado automáticamente $count sesiones antiguas de asistencia.");
        } else {
            mtrace('No se encontraron sesiones para bloquear automáticamente.');
        }
        
        mtrace('Tarea de bloqueo automático de sesiones de asistencia completada.');
    }
} 
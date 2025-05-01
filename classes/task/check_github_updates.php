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
 * Tarea programada para comprobar actualizaciones desde GitHub.
 *
 * @package    mod_attendance
 * @copyright  2023 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_attendance\task;

defined('MOODLE_INTERNAL') || die();

/**
 * Tarea programada para comprobar periódicamente actualizaciones desde GitHub.
 */
class check_github_updates extends \core\task\scheduled_task {

    /**
     * Obtiene el nombre de la tarea.
     *
     * @return string Nombre descriptivo de la tarea
     */
    public function get_name() {
        return get_string('checkupdatesscheduledtask', 'mod_attendance');
    }

    /**
     * Ejecuta la tarea de comprobación de actualizaciones.
     */
    public function execute() {
        mtrace('Iniciando comprobación de actualizaciones desde GitHub para el plugin de asistencia...');
        
        // Verificar si la comprobación de actualizaciones está habilitada
        $enabled = get_config('mod_attendance', 'habilitaractualizacion');
        if (!$enabled) {
            mtrace('La comprobación de actualizaciones está deshabilitada. Saltando tarea.');
            return;
        }
        
        // Obtener la configuración del repositorio
        $owner = get_config('mod_attendance', 'githubusuario');
        $repo = get_config('mod_attendance', 'githubrepo');
        
        if (empty($owner) || empty($repo)) {
            mtrace('Error: No se ha configurado el repositorio de GitHub. Saltando tarea.');
            return;
        }
        
        // Verificar si es tiempo de comprobar actualizaciones basado en la frecuencia
        $frequency = get_config('mod_attendance', 'actualizacionfrecuencia') ?: 'weekly';
        $lastcheck = get_config('mod_attendance', 'ultimacomprobacion') ?: 0;
        
        $checkinterval = $this->get_check_interval($frequency);
        $timenow = time();
        
        if (($timenow - $lastcheck) < $checkinterval) {
            mtrace('No es tiempo de comprobar actualizaciones todavía. Saltando tarea.');
            return;
        }
        
        // Comprobar actualizaciones
        $updater = new \mod_attendance\github_updater($owner, $repo);
        $updateinfo = $updater->check_for_updates();
        
        // Actualizar la hora de la última comprobación
        set_config('ultimacomprobacion', $timenow, 'mod_attendance');
        
        if ($updateinfo) {
            mtrace('Actualización disponible: Versión ' . $updateinfo->latest_version);
            set_config('actualizaciondisponible', 1, 'mod_attendance');
            set_config('ultimaversiondisponible', $updateinfo->latest_version, 'mod_attendance');
            set_config('enlaceactualizacion', $updateinfo->download_url, 'mod_attendance');
        } else {
            mtrace('No hay actualizaciones disponibles.');
            set_config('actualizaciondisponible', 0, 'mod_attendance');
        }
        
        mtrace('Comprobación de actualizaciones completada.');
    }
    
    /**
     * Obtiene el intervalo de tiempo entre comprobaciones según la frecuencia configurada.
     *
     * @param string $frequency Frecuencia de comprobación (daily, weekly, monthly)
     * @return int Intervalo en segundos
     */
    private function get_check_interval($frequency) {
        switch ($frequency) {
            case 'daily':
                return DAYSECS;
            case 'weekly':
                return WEEKSECS;
            case 'monthly':
                return DAYSECS * 30;
            default:
                return WEEKSECS;
        }
    }
} 
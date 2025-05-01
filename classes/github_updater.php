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
 * Clase para comprobar actualizaciones del plugin desde GitHub
 *
 * @package    mod_attendance
 * @copyright  2023 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_attendance;

defined('MOODLE_INTERNAL') || die();

/**
 * Clase para comprobar y notificar sobre actualizaciones del plugin desde GitHub.
 */
class github_updater {
    
    /** @var string URL base de la API de GitHub */
    const GITHUB_API_URL = 'https://api.github.com';
    
    /** @var string Nombre del propietario del repositorio en GitHub */
    private $owner;
    
    /** @var string Nombre del repositorio en GitHub */
    private $repo;
    
    /**
     * Constructor.
     *
     * @param string $owner Nombre del propietario del repositorio en GitHub
     * @param string $repo Nombre del repositorio en GitHub
     */
    public function __construct($owner, $repo) {
        $this->owner = $owner;
        $this->repo = $repo;
    }
    
    /**
     * Comprueba si hay actualizaciones disponibles para el plugin.
     *
     * @return object|false Información de la última versión si hay una actualización, false en caso contrario
     */
    public function check_for_updates() {
        global $CFG;
        
        // Obtener la versión actual del plugin
        $currentversion = $this->get_current_version();
        if (!$currentversion) {
            return false;
        }
        
        // Obtener la última versión disponible en GitHub
        $latestrelease = $this->get_latest_release();
        if (!$latestrelease) {
            return false;
        }
        
        // Comparar versiones para determinar si hay actualización
        $latestversionstring = ltrim($latestrelease->tag_name, 'v');
        
        // Si la versión en GitHub es mayor, hay actualización disponible
        if (version_compare($latestversionstring, $currentversion->release, '>')) {
            return (object)[
                'current_version' => $currentversion->release,
                'latest_version' => $latestversionstring,
                'release_info' => $latestrelease,
                'download_url' => $latestrelease->zipball_url,
                'release_date' => strtotime($latestrelease->published_at),
                'release_notes' => $latestrelease->body
            ];
        }
        
        return false;
    }
    
    /**
     * Obtiene la información de la versión actual del plugin.
     *
     * @return object|false Información de la versión actual, false en caso de error
     */
    private function get_current_version() {
        global $CFG;
        
        $pluginman = \core_plugin_manager::instance();
        $plugininfo = $pluginman->get_plugin_info('mod_attendance');
        
        if (!$plugininfo) {
            return false;
        }
        
        return (object)[
            'version' => $plugininfo->versiondisk,
            'release' => $plugininfo->release
        ];
    }
    
    /**
     * Obtiene información sobre la última versión disponible en GitHub.
     *
     * @return object|false Información de la última versión, false en caso de error
     */
    private function get_latest_release() {
        $url = self::GITHUB_API_URL . "/repos/{$this->owner}/{$this->repo}/releases/latest";
        
        $curl = new \curl();
        $curl->setopt(array(
            'CURLOPT_RETURNTRANSFER' => true,
            'CURLOPT_USERAGENT' => 'Moodle-Attendance-Updater',
            'CURLOPT_CONNECTTIMEOUT' => 30,
            'CURLOPT_HEADER' => false
        ));
        
        $response = $curl->get($url);
        
        if ($curl->get_errno()) {
            return false;
        }
        
        $data = json_decode($response);
        
        if (empty($data) || isset($data->message)) {
            return false;
        }
        
        return $data;
    }
    
    /**
     * Muestra una notificación en la interfaz de Moodle si hay actualizaciones disponibles.
     *
     * @param bool $forcecheck Forzar la comprobación de actualizaciones
     * @return bool True si se mostró una notificación, false en caso contrario
     */
    public function display_update_notification($forcecheck = false) {
        global $PAGE, $OUTPUT, $SESSION;
        
        // Solo comprobar una vez por sesión a menos que se fuerce
        if (!$forcecheck && isset($SESSION->mod_attendance_update_checked)) {
            return false;
        }
        
        $SESSION->mod_attendance_update_checked = true;
        
        // Solo mostrar a administradores
        if (!has_capability('moodle/site:config', \context_system::instance())) {
            return false;
        }
        
        $updateinfo = $this->check_for_updates();
        
        if ($updateinfo) {
            $message = get_string('updateavailable', 'mod_attendance', $updateinfo);
            $url = new \moodle_url('/admin/settings.php', ['section' => 'modsettingattendance']);
            
            $notification = new \core\output\notification(
                $message,
                \core\output\notification::NOTIFY_INFO
            );
            $notification->set_show_closebutton(true);
            
            echo $OUTPUT->render($notification);
            return true;
        }
        
        return false;
    }
    
    /**
     * Obtiene la URL del formulario para reportar problemas en GitHub.
     *
     * @return string URL del formulario de reporte de problemas
     */
    public function get_issue_report_url() {
        return "https://github.com/{$this->owner}/{$this->repo}/issues/new";
    }
} 
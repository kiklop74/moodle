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
 * Grade exchange web service
 *
 * @package    local_xrayws
 * @copyright  2015 Moodlerooms {@link http://www.moodlerooms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Darko Miletic <darko.miletic@gmail.com>
 */

defined('MOODLE_INTERNAL') || die();

/* @var $CFG stdclass */
require_once("$CFG->libdir/externallib.php");

/**
 * Few help links
 *
 * https://docs.moodle.org/dev/Web_services_files_handling#File_download
 * https://docs.moodle.org/dev/Task_API
 *
 */

/**
 * Soomo external functions for grade exchange
 * @package    local_xrayws
 * @category   external
 * @copyright  2015 Moodlerooms {@link http://www.moodlerooms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 2.5
 */
class local_xrayws_external extends external_api {

    /**
     * get_version parameters
     *
     * @return external_function_parameters
     */
    public static function get_version_parameters() {
        return new external_function_parameters(array());
    }

    /**
     * Returns single textual value
     *
     * @return external_value
     */
    public static function get_version_returns() {
        return new external_value(PARAM_TEXT, 'event name', VALUE_REQUIRED, '', NULL_NOT_ALLOWED);
    }

    /**
     * @return string
     */
    public static function get_version() {
        $result = null;
        $plugin = core_plugin_manager::instance()->get_plugin_info('local_xrayws');
        if ($plugin !== null) {
            $result = (string)$plugin->versiondisk;
        }
        return $result;
    }

    /**
     * @return external_function_parameters
     */
    public static function get_data_parameters() {
        return new external_function_parameters(
            array('date' => new external_value(PARAM_TEXT, 'Last access date', VALUE_REQUIRED))
        );
    }

    /**
     * @return null
     */
    public static function get_data_returns() {
        return null;
    }

    /**
     * @param $data
     * @return external_multiple_structure
     */
    public static function get_data($date) {
        return null;
    }

}

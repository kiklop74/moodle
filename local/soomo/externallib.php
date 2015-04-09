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
 * @package    local_soomo
 * @copyright  2015 Soomo Learning {@link http://www.soomolearning.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Darko Miletic <darko.miletic@gmail.com>
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/externallib.php");

/**
 * Soomo external functions for grade exchange
 * @package    local_soomo
 * @category   external
 * @copyright  2015 Soomo Learning {@link http://www.soomolearning.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 2.7
 */
class local_soomo_external extends external_api {

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
        $plugin = core_plugin_manager::instance()->get_plugin_info('local_soomo');
        if ($plugin !== null) {
            $result = (string)$plugin->versiondisk;
        }
        return $result;
    }

    /**
     * @param string|null $userid
     * @param string|null $email
     * @return external_multiple_structure
     */
    protected static function get_user($userid = null, $email = null) {
        // TODO: implement this
        return null;
    }

    /**
     * @return null
     */
    protected static function get_user_returns() {
        // TODO: implement this
        return null;
    }

    /**
     * @return external_function_parameters
     */
    public static function get_user_byid_parameters() {
        return new external_function_parameters(
            array('userid' => new external_value(PARAM_TEXT, 'User id', VALUE_REQUIRED))
        );
    }

    /**
     * @return null
     */
    public static function get_user_byid_returns() {
        return self::get_user_returns();
    }

    /**
     * @param $userid
     * @return external_multiple_structure
     */
    public static function get_user_byid($userid) {
        return self::get_user($userid);
    }

    /**
     * @return external_function_parameters
     */
    public static function get_user_byemail_parameters() {
        return new external_function_parameters(
            array('email' => new external_value(PARAM_EMAIL, 'User email', VALUE_REQUIRED))
        );
    }

    /**
     * @return null
     */
    public static function get_user_byemail_returns() {
        return self::get_user_returns();
    }

    /**
     * @param $email
     * @return external_multiple_structure
     */
    public static function get_user_byemail($email) {
        return self::get_user(null, $email);
    }

    /**
     * @return external_function_parameters
     */
    public static function get_course_parameters() {
        return new external_function_parameters(
            array('courseid' => new external_value(PARAM_TEXT, 'Course id', VALUE_REQUIRED))
        );
    }

    /**
     * @return null
     */
    public static function get_course_returns() {
        // TODO: implement this
        return null;
    }

    /**
     * @param mixed $courseid
     * @return null
     */
    public static function get_course($courseid) {
        // TODO: implement this
        return null;
    }

    public static function create_grades_parameters() {
        return new external_function_parameters(
            array('grades' => new external_multiple_structure(
                new external_single_structure(
                    array(
                        'name' => new external_value(PARAM_TEXT, 'event name', VALUE_REQUIRED, '', NULL_NOT_ALLOWED),
                        'description' => new external_value(PARAM_RAW, 'Description', VALUE_DEFAULT, null, NULL_ALLOWED),
                        'format' => new external_format_value('description', VALUE_DEFAULT),
                        'courseid' => new external_value(PARAM_INT, 'course id', VALUE_DEFAULT, 0, NULL_NOT_ALLOWED),
                        'groupid' => new external_value(PARAM_INT, 'group id', VALUE_DEFAULT, 0, NULL_NOT_ALLOWED),
                        'repeats' => new external_value(PARAM_INT, 'number of repeats', VALUE_DEFAULT, 0, NULL_NOT_ALLOWED),
                        'eventtype' => new external_value(PARAM_TEXT, 'Event type', VALUE_DEFAULT, 'user', NULL_NOT_ALLOWED),
                        'timestart' => new external_value(PARAM_INT, 'timestart', VALUE_DEFAULT, time(), NULL_NOT_ALLOWED),
                        'timeduration' => new external_value(PARAM_INT, 'time duration', VALUE_DEFAULT, 0, NULL_NOT_ALLOWED),
                        'visible' => new external_value(PARAM_INT, 'visible', VALUE_DEFAULT, 1, NULL_NOT_ALLOWED),
                        'sequence' => new external_value(PARAM_INT, 'sequence', VALUE_DEFAULT, 1, NULL_NOT_ALLOWED),
                    ), 'grade')
            )
            )
        );

    }

    /**
     * @return null
     */
    public static function create_grades_returns() {
        // TODO: implement this
        return null;
    }

    /**
     * @param mixed $courseid
     * @return null
     */
    public static function create_grades($courseid) {
        // TODO: implement this
        return null;
    }


}

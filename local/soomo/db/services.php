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
 * Grade exchange web service definition
 *
 * @package    local_soomo
 * @category   webservice
 * @copyright  2015 Soomo Learning {@link http://www.soomolearning.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Darko Miletic <darko.miletic@gmail.com>
 */

$functions = array(

    'local_soomo_get_version' => array(
        'classname'   => 'local_soomo_external',
        'methodname'  => 'get_version',
        'classpath'   => 'local/soomo/externallib.php',
        'description' => 'Returns Webservice API version.',
        'type'        => 'read',
    ),

    'local_soomo_get_user_byid' => array(
        'classname'   => 'local_soomo_external',
        'methodname'  => 'get_user_byid',
        'classpath'   => 'local/soomo/externallib.php',
        'description' => 'Returns user information based on LMS user id.',
        'type'        => 'read',
    ),

    'local_soomo_get_user_byemail' => array(
        'classname'   => 'local_soomo_external',
        'methodname'  => 'get_user_byemail',
        'classpath'   => 'local/soomo/externallib.php',
        'description' => 'Returns user information based on user email.',
        'type'        => 'read',
    ),

    'local_soomo_get_course' => array(
        'classname'   => 'local_soomo_external',
        'methodname'  => 'get_course',
        'classpath'   => 'local/soomo/externallib.php',
        'description' => 'Returns course information based on LMS course id.',
        'type'        => 'read',
    ),

    /*
    'local_soomo_update_grade' => array(
        'classname'   => 'local_soomo_external',
        'methodname'  => 'update_grade',
        'classpath'   => 'local/soomo/externallib.php',
        'description' => "Updates/set's individual grade within course.",
        'type'        => 'write',
    ),

    'local_soomo_delete_grade' => array(
        'classname'   => 'local_soomo_external',
        'methodname'  => 'delete_grade',
        'classpath'   => 'local/soomo/externallib.php',
        'description' => 'Removes a specific grade.',
        'type'        => 'write',
    ),

    'local_soomo_get_grades' => array(
        'classname'   => 'local_soomo_external',
        'methodname'  => 'get_grades',
        'classpath'   => 'local/soomo/externallib.php',
        'description' => 'Returns course grades.',
        'type'        => 'read',
    ),
*/
);

$services = array(
    get_string('wsname', 'local_soomo') => array (
        'functions'       => array(
            'local_soomo_get_version'     ,
            'local_soomo_get_user_byid'   ,
            'local_soomo_get_user_byemail',
            'local_soomo_get_course'      ,
//            'local_soomo_create_grades'   ,
//            'local_soomo_update_grade'    ,
//            'local_soomo_delete_grade'    ,
//            'local_soomo_get_grades'
        ),
        'enabled'         => false,
        'restrictedusers' => false,
        'shortname'       => 'soomo_web_service',
        'downloadfiles'   => false,
        'uploadfiles'     => false
    )
);

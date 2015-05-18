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
 * @package    local_xrayws
 * @category   webservice
 * @copyright  2015 Moodlerooms {@link http://www.moodlerooms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Darko Miletic <darko.miletic@gmail.com>
 */

$functions = array(

    'local_xrayws_get_version' => array(
        'classname'   => 'local_xrayws_external',
        'methodname'  => 'get_version',
        'classpath'   => 'local/xrayws/externallib.php',
        'description' => get_string('local_xrayws_get_version_desc', 'local_xrayws'),
        'type'        => 'read',
    ),

    'local_xrayws_get_data' => array(
        'classname'   => 'local_xrayws_external',
        'methodname'  => 'get_data',
        'classpath'   => 'local/xrayws/externallib.php',
        'description' => get_string('local_xrayws_get_data_desc', 'local_xrayws'),
        'type'        => 'read',
    ),

);

$services = array(
    get_string('wsname', 'local_xrayws') => array (
        'functions'       => array(
            'local_xrayws_get_version',
            'local_xrayws_get_data'   ,
        ),
        'enabled'         => false,
        'restrictedusers' => false,
        'shortname'       => 'xray_web_service',
        'downloadfiles'   => true,
        'uploadfiles'     => false
    )
);

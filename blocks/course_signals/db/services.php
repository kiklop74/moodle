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
 * External course participation api.
 *
 * This api is mostly read only, the actual enrol and unenrol
 * support is in each enrol plugin.
 *
 * @package    course_signals
 * @copyright  2012 Moodlerooms inc (http://moodlerooms.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// We defined the web service functions to install.
$functions = array(
        'block_course_signals_get_sections' => array(
                'classname'      => 'moodle_block_course_signals_get_sections_external',
                'methodname'     => 'get_sections',
                'classpath'      => 'blocks/course_signals/externallib.php',
                'description'    => 'Return courses',
                'type'           => 'read',
                'testclientpath' => 'blocks/course_signals/testclient_forms.php',
        ),
        'block_course_signals_get_students' => array(
                'classname'      => 'moodle_block_course_signals_get_students_external',
                'methodname'     => 'get_students',
                'classpath'      => 'blocks/course_signals/externallib.php',
                'description'    => 'Return students',
                'type'           => 'read',
                'testclientpath' => 'blocks/course_signals/testclient_forms.php',
        ),
        'block_course_signals_get_effort' => array(
                'classname'      => 'moodle_block_course_signals_get_effort_external',
                'methodname'     => 'get_effort',
                'classpath'      => 'blocks/course_signals/externallib.php',
                'description'    => 'Return effort data',
                'type'           => 'read',
                'testclientpath' => 'blocks/course_signals/testclient_forms.php',
        ),
        'block_course_signals_are_assesments_available' => array(
                'classname'      => 'moodle_block_course_signals_are_assesments_available_external',
                'methodname'     => 'are_assesments_available',
                'classpath'      => 'blocks/course_signals/externallib.php',
                'description'    => 'Are assesments available',
                'type'           => 'read',
                'testclientpath' => 'blocks/course_signals/testclient_forms.php',
        ),
        'block_course_signals_get_assesments' => array(
                'classname'      => 'moodle_block_course_signals_get_assesments_external',
                'methodname'     => 'get_assesments',
                'classpath'      => 'blocks/course_signals/externallib.php',
                'description'    => 'Get assesments',
                'type'           => 'read',
                'testclientpath' => 'blocks/course_signals/testclient_forms.php',
        ),
        'block_course_signals_get_assesment_results' => array(
                'classname'      => 'moodle_block_course_signals_get_assesment_results_external',
                'methodname'     => 'get_assesment_results',
                'classpath'      => 'blocks/course_signals/externallib.php',
                'description'    => 'Get assesment results',
                'type'           => 'read',
                'testclientpath' => 'blocks/course_signals/testclient_forms.php',
        ),
        'block_course_signals_get_faculty' => array(
                'classname'      => 'moodle_block_course_signals_get_faculty_external',
                'methodname'     => 'get_faculty',
                'classpath'      => 'blocks/course_signals/externallib.php',
                'description'    => 'Get faculty',
                'type'           => 'read',
                'testclientpath' => 'blocks/course_signals/testclient_forms.php',
        ),
        'block_course_signals_get_student' => array(
                'classname'      => 'moodle_block_course_signals_get_student_external',
                'methodname'     => 'get_student',
                'classpath'      => 'blocks/course_signals/externallib.php',
                'description'    => 'Get Student',
                'type'           => 'read',
                'testclientpath' => 'blocks/course_signals/testclient_forms.php',
        ),
        'block_course_signals_get_facultyroles' => array(
                'classname'      => 'moodle_block_course_signals_get_facultyroles_external',
                'methodname'     => 'get_facultyroles',
                'classpath'      => 'blocks/course_signals/externallib.php',
                'description'    => 'Get faculty roles',
                'type'           => 'read',
                'testclientpath' => 'blocks/course_signals/testclient_forms.php',
        ),
        'block_course_signals_get_studentroles' => array(
                'classname'      => 'moodle_block_course_signals_get_studentroles_external',
                'methodname'     => 'get_studentroles',
                'classpath'      => 'blocks/course_signals/externallib.php',
                'description'    => 'Get student roles',
                'type'           => 'read',
                'testclientpath' => 'blocks/course_signals/testclient_forms.php',
        ),
        'block_course_signals_get_lmsusers' => array(
                'classname'      => 'moodle_block_course_signals_get_lmsusers_external',
                'methodname'     => 'get_lmsusers',
                'classpath'      => 'blocks/course_signals/externallib.php',
                'description'    => 'Get lms users',
                'type'           => 'read',
                'testclientpath' => 'blocks/course_signals/testclient_forms.php',
        ),
        'block_course_signals_get_extusers' => array(
                'classname'      => 'moodle_block_course_signals_get_extusers_external',
                'methodname'     => 'get_extusers',
                'classpath'      => 'blocks/course_signals/externallib.php',
                'description'    => 'Get ext users',
                'type'           => 'read',
                'testclientpath' => 'blocks/course_signals/testclient_forms.php',
        )

);

// We define the services to install as pre-build services. A pre-build service is not editable by administrator.
$services = array(
        'Course signals' => array(
                'functions'       => array (
                                            'block_course_signals_get_sections',
                                            'block_course_signals_get_students',
                                            'block_course_signals_get_effort',
                                            'block_course_signals_are_assesments_available',
                                            'block_course_signals_get_assesments',
                                            'block_course_signals_get_assesment_results',
                                            'block_course_signals_get_faculty',
                                            'block_course_signals_get_student',
											'block_course_signals_get_facultyroles',
                                            'block_course_signals_get_studentroles',
                                            'block_course_signals_get_lmsusers',
											'block_course_signals_get_extusers'
                                            ),
                'restrictedusers' => 0,
                'enabled'         => 1
        )
);

$version = (float)get_config('moodle', 'version');

if ($version < 2011120500.00) {
    //pre Moodle 2.2 method names
    $services['Course signals']['functions'][] = 'moodle_course_get_courses';
    $services['Course signals']['functions'][] = 'moodle_user_get_users_by_courseid';
    $services['Course signals']['functions'][] = 'moodle_group_get_course_groups';
    $services['Course signals']['functions'][] = 'moodle_group_get_groupmembers';
    $services['Course signals']['functions'][] = 'moodle_user_get_users_by_id';
    if ($version >= 2011070100.00) {
        //This method was introduced in Moodle 2.1
        $services['Course signals']['functions'][] = 'moodle_user_get_course_participants_by_id';
    }
} else {
    //Moodle 2.2 and onward method names
    $services['Course signals']['functions'][] = 'core_course_get_courses';
    $services['Course signals']['functions'][] = 'core_enrol_get_enrolled_users';
    $services['Course signals']['functions'][] = 'core_group_get_course_groups';
    $services['Course signals']['functions'][] = 'core_group_get_group_members';
    $services['Course signals']['functions'][] = 'core_user_get_users_by_id';
    $services['Course signals']['functions'][] = 'core_user_get_course_user_profiles';
}

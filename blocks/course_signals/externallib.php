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
 * @subpackage webservice
 * @copyright  2012 Moodlerooms inc (http://moodlerooms.com)
 * @author     Pablo Pagnone <ppagnone@moodlerooms.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/externallib.php");
require_once($CFG->dirroot.'/blocks/course_signals/lib/section.php');
require_once($CFG->dirroot.'/blocks/course_signals/lib/utils.php');
require_once($CFG->dirroot.'/blocks/edit_form.php');
require_once($CFG->dirroot.'/blocks/course_signals/edit_form.php');

class moodle_block_course_signals_get_sections_external extends external_api {

    /**
     * @return external_function_parameters
     */
    public static function get_sections_parameters() {
        $parameters = array();
        $parameters['faculty'  ] = new external_value(PARAM_INT , 'desc', VALUE_REQUIRED);
        $parameters['term'     ] = new external_value(PARAM_TEXT, 'desc', VALUE_REQUIRED);
        $parameters['startdate'] = new external_value(PARAM_TEXT, 'desc', VALUE_OPTIONAL); //format YYYY-MM-DD
        $options = array('options' => new external_single_structure($parameters));
        return new external_function_parameters($options);
    }

    /**
     * @return external_multiple_structure
     */
    public static function get_sections_returns() {
        $sectiondef = array();
        $sectiondef['sourcedid'  ] = new external_value(PARAM_INT , 'parent id'      , VALUE_REQUIRED);
        $sectiondef['courselmsid'] = new external_value(PARAM_INT , 'course id'      , VALUE_REQUIRED);
        $sectiondef['grouplmsid' ] = new external_value(PARAM_TEXT, 'course idnumber', VALUE_OPTIONAL);
        return new external_multiple_structure(new external_single_structure($sectiondef, 'section'));
    }

    /**
     * @param mixed $options
     * @return array
     */
    public static function get_sections($options) {

        global $DB;
        $result = array();

        //Validate and prepare input parameters
        $params = self::validate_parameters(self::get_sections_parameters(),
                                            array('options' => $options));


        //get the parameters
        $facultyid = $params['options']['faculty']; //user id in LMS
        $term = $params['options']['term'];  //course's identificator - vinculated with field selected in config
        $startdate = null;

        if(isset($params['options']['startdate']) && !empty($params['options']['startdate'])){
            //format must be YYYY-MM-DD
            $startdate = strtotime($params['options']['startdate']);
            if(!$startdate){
                throw new invalid_response_exception(get_string('error_format_date',
                                                    			'block_course_signals'));
            }
        }

        //get courses where user $facultyid has teacher role (seted in config block)
        $roles = block_course_signals_utils::get_config_field_roles_teacher();

        //get courses where teacher is enrolled
        $courses_enrolled = block_course_signals_utils::get_courses_where_user_is_enrolled($facultyid,
                                                                                           $roles,
                                                                                           $term,
                                                                                           $startdate);
        if(empty($courses_enrolled)){
            throw new invalid_response_exception(get_string('error_user_is_not_enrolled_in_with_faculty_role',
                            								'block_course_signals'));
        }
        foreach($courses_enrolled as $course){
            $courses[] = $course->id;
        }

        $result = block_course_signals_utils::get_sections($courses);
        if(empty($result)){
            //not exist user with role teacher enrolled in any course
            throw new invalid_response_exception(get_string('error_not_exist_section_for_user',
                                        					'block_course_signals'));
        }

        return $result;
    }
}

class moodle_block_course_signals_get_students_external extends external_api {

    /**
     * @return external_function_parameters
     */
    public static function get_students_parameters(){
        $options = array('section' => new external_value(PARAM_TEXT, 'desc', VALUE_REQUIRED));
        return new external_function_parameters($options);
    }

    /**
     * @return external_multiple_structure
     */
    public static function get_students_returns(){
        $userdef = array();
        $userdef['id'       ] = new external_value(PARAM_INT , 'user id'      , VALUE_REQUIRED);
        $userdef['idnumber' ] = new external_value(PARAM_TEXT, 'user idnumber', VALUE_OPTIONAL);
        $userdef['firstname'] = new external_value(PARAM_TEXT, 'firstname'    , VALUE_REQUIRED);
        $userdef['lastname' ] = new external_value(PARAM_TEXT, 'lastname'     , VALUE_REQUIRED);
        $userdef['email'    ] = new external_value(PARAM_TEXT, 'email'        , VALUE_REQUIRED);
        return new external_multiple_structure(new external_single_structure($userdef, 'student'));
    }

    /**
     * @param mixed $section
     * @return array
     */
    public static function get_students($section){

        //Validate and prepare input parameters
        $params = self::validate_parameters(self::get_students_parameters(),
        array('section' => $section));

        //get the parameters
        $section_value = $params['section'];

        //create return value - an example of the output format
        $result = array();
        $result[] = array('id'        => 1,
                          'idnumber'  => 'sampleid123',
                          'firstname' => 'John',
                          'lastname'  => 'Doe',
                          'email'     => 'jdoe@email.net'
                         );
                // add more here

                //TODO: Method suspended in last documentation

        return $result;
    }
}

class moodle_block_course_signals_get_effort_external extends external_api {

    /**
     * @return external_function_parameters
     */
    public static function get_effort_parameters() {
        $parameters = array();
        $parameters['sourcedid'  ] = new external_value(PARAM_TEXT, 'desc', VALUE_OPTIONAL);
        $parameters['courselmsid'] = new external_value(PARAM_INT, 'desc', VALUE_OPTIONAL);
        $parameters['userlmsid'  ] = new external_multiple_structure(new external_value(PARAM_INT, 'user lms id'),'Users ids lms', VALUE_OPTIONAL);
        $parameters['startdate'  ] = new external_value(PARAM_TEXT, 'desc', VALUE_OPTIONAL);
        $parameters['enddate'    ] = new external_value(PARAM_TEXT, 'desc', VALUE_OPTIONAL);
        $options = array('options' => new external_single_structure($parameters));
        return new external_function_parameters($options);
    }

    /**
     * @return external_multiple_structure
     */
    public static function get_effort_returns() {
        $effortdef = array();
        $effortdef['id'                    ] = new external_value(PARAM_INT , 'user id'      , VALUE_REQUIRED);
        $effortdef['sourcedid'             ] = new external_value(PARAM_TEXT, 'user sourcedid', VALUE_REQUIRED);
        $effortdef['firstname'             ] = new external_value(PARAM_TEXT, 'firstname'    , VALUE_REQUIRED);
        $effortdef['lastname'              ] = new external_value(PARAM_TEXT, 'lastname'     , VALUE_REQUIRED);
        $effortdef['email'                 ] = new external_value(PARAM_TEXT, 'email'        , VALUE_REQUIRED);
        $effortdef['AssessmentsBegun'      ] = new external_value(PARAM_INT, 'AssessmentsBegun');
        $effortdef['AssessmentsFinished'   ] = new external_value(PARAM_INT, 'AssessmentsFinished');
        $effortdef['AssignmentsRead'       ] = new external_value(PARAM_INT, 'AssignmentsRead');
        $effortdef['AssignmentsSubmitted'  ] = new external_value(PARAM_INT, 'AssignmentsSubmitted');
        $effortdef['CalendarEntriesRead'   ] = new external_value(PARAM_INT, 'CalendarEntriesRead');
        $effortdef['ContentPagesViewed'    ] = new external_value(PARAM_INT, 'ContentPagesViewed');
        $effortdef['DiscussionPostsCreated'] = new external_value(PARAM_INT, 'DiscussionPostsCreated');
        $effortdef['DiscussionPostsRead'   ] = new external_value(PARAM_INT, 'DiscussionPostsRead');
        $effortdef['NumberCMSSessions'     ] = new external_value(PARAM_INT, 'NumberCMSSessions');
        $effortdef['OrganizerPagesViewed'  ] = new external_value(PARAM_INT, 'OrganizerPagesViewed');

        return new external_multiple_structure(new external_single_structure($effortdef, 'effort'));
    }

    /**
     * @param mixed $options
     * @return array
     */
    public static function get_effort($options) {

        global $CFG;

        //Validate and prepare input parameters
        $params = self::validate_parameters(self::get_effort_parameters(),
                                            array('options' => $options));

        //get the parameters
        $sourceid = null;
        $courselmsid = null;
        $userlmsid = '';
        $startdate = '';
        $enddate = '';

        $courses = array();
        $limit = 0;

        if(isset($params['options']['sourcedid']) && !empty($params['options']['sourcedid'])){
            //if exist param sourcedid only return one section
            //(maybe exist 2 or more if idnumber is not correct configurated)
            $sourceid = $params['options']['sourcedid'];
            $limit = 1;
        }
        if(isset($params['options']['courselmsid']) && !empty($params['options']['courselmsid'])){
            $courselmsid = $params['options']['courselmsid'];
            $courses = array($courselmsid);
        }
        if(isset($params['options']['userlmsid']) && !empty($params['options']['userlmsid'])){
            $userlmsid = $params['options']['userlmsid'];
        }
        if(isset($params['options']['startdate']) && !empty($params['options']['startdate'])){
            $startdate = $params['options']['startdate'];
        }
        if(isset($params['options']['enddate']) && !empty($params['options']['enddate'])){
            $enddate = $params['options']['enddate'];
        }

        //TODO:: implement startdate and enddate

        if(empty($sourceid) && empty($courselmsid)){
            //one parameter is required
            throw new invalid_response_exception(get_string('error_geteffort_params',
                                                        	'block_course_signals'));
        }

        //get sections
        $sections = block_course_signals_utils::get_sections($courses ,$sourceid, $limit);
        if(empty($sections)){
            //not exist section
            throw new invalid_response_exception(get_string('error_section_not_exist',
                                                            'block_course_signals'));
        }
        if(empty($courselmsid)){
            //obtain id of first register
            $courselmsid = $sections[0]['courselmsid'];
        }

        $block_config_course = block_course_signals_utils::get_config_block($courselmsid);

        $groups_to_get_stats = array(); //groups users for get statistics
        $courses_to_get_stats = array(); //courses where I will search statistics, course are part of sections

        foreach($sections as $section){

            switch($section['type']){

                case block_course_signals_section::TYPE_CUSTOMPARENT:
                    if($block_config_course->statme){
                        if(!in_array($courses_to_get_stats, $section["courselmsid"])){
                            $courses_to_get_stats[] = $section["courselmsid"];
                        }

                    }
                    if($block_config_course->statparent){
                        if(!in_array($courses_to_get_stats, $section["parentcourseid"])){
                            $courses_to_get_stats[] = $section["parentcourseid"];
                        }
                    }
                break;

                case block_course_signals_section::TYPE_METACOURSE:

                    //TODO::config block is confuzed , view "Use statistics from parent course.";
                    //maybe could be "Use statistics from parent course or child of metacourse"
                    if($block_config_course->statme){
                        if(!in_array($courses_to_get_stats, $section["courselmsid"])){
                            $courses_to_get_stats[] = $section["courselmsid"];
                        }
                    }
                    if($block_config_course->statparent){
                        if(!in_array($courses_to_get_stats, $section["childcourseid"])){
                            $courses_to_get_stats[] = $section["childcourseid"];
                        }
                    }

                break;

                case block_course_signals_section::TYPE_GROUP:

                    //get users results only of users groups
                    if(!in_array($section["grouplmsid"], $groups_to_get_stats)){
                        $groups_to_get_stats[] = $section["grouplmsid"];
                    }

                    if(!in_array($courses_to_get_stats, $section["courselmsid"])){
                        $courses_to_get_stats[] = $section["courselmsid"];
                    }

                break;

                case block_course_signals_section::TYPE_COURSE:

                    if(!in_array($courses_to_get_stats, $section["courselmsid"])){
                        $courses_to_get_stats[] = $section["courselmsid"];
                    }

                break;
            }
        }

        if(empty($courses_to_get_stats)){
            //section is not configurated to get statistics (not parentstat and not coursestat)
            throw new invalid_response_exception(get_string('section_is_not_configurated_to_stats',
                                            				'block_course_signals'));
        }

        //only return users gradeables
        $roles_students = explode(',', $CFG->gradebookroles);

        if($section['type'] == block_course_signals_section::TYPE_GROUP){
            //get users groups
            $users_to_get_stats = block_course_signals_utils::get_users_from_groups($groups_to_get_stats, $roles_students);
        }else{
            //get users courses
            $users_to_get_stats = block_course_signals_utils::get_users_from_courses($courses_to_get_stats, $roles_students);
        }


        //check users id received by params
        if(!empty($userlmsid) && is_array($userlmsid)){

            //check if users received by webservice are enrolled in section
            $diff = array_diff($userlmsid, $users_to_get_stats);
            if(!empty($diff)){
                //users are not enrolled in section
                $ids = implode(',', $diff);
                throw new invalid_response_exception(get_string('error_not_exist_users_in_section',
                                                                'block_course_signals',$ids));
            }

            //get only users received by webservice
            $users_to_get_stats = $userlmsid;
        }


        if(empty($users_to_get_stats)){
            //not exist users to get stats
            throw new invalid_response_exception(get_string('error_not_exist_users_enrolled_in_section',
                                                        	'block_course_signals'));
        }

        $stats = array();
        foreach ($users_to_get_stats as $userid){
            //get statistics by user
            $get_stats = block_course_signals_utils::get_statistics($userid, $courses_to_get_stats);
            if(!empty($get_stats)){
                $stats[] = $get_stats;
            }
        }

       return $stats;
    }

}

class moodle_block_course_signals_are_assesments_available_external extends external_api {

    /**
     * @return external_function_parameters
     */
    public static function are_assesments_available_parameters() {
        $parameters = array();
        $parameters['sourcedid'  ] = new external_value(PARAM_TEXT , 'desc', VALUE_OPTIONAL);
        $parameters['courselmsid'] = new external_value(PARAM_TEXT, 'desc', VALUE_OPTIONAL);

        $options = array('options' => new external_single_structure($parameters));
        return new external_function_parameters($options);
    }

    /**
     * @return external_value
     */
    public static function are_assesments_available_returns() {

        $assessment = array();
        $assessment['id'  ] = new external_value(PARAM_INT , 'id quiz', VALUE_REQUIRED);
        $assessment['name'] = new external_value(PARAM_TEXT, 'name quiz', VALUE_REQUIRED);
        $assessment['highscore'] = new external_value(PARAM_TEXT, 'highscore', VALUE_REQUIRED);
        return new external_multiple_structure(new external_single_structure($assessment, 'assessments for section'));
    }

    /**
     * Check if exist quizzes in course
     * If parameter is courseidlms search in course.
     * If parameter is sectionid, get first section and check in his course if exist quizzes.
     *
     * @param mixed $options
     * @return boolean
     */
    public static function are_assesments_available($options) {

        global $DB;

        //Validate and prepare input parameters
        $params = self::validate_parameters(self::are_assesments_available_parameters(),
                                            array('options' => $options));

        //get the parameters
        $sectionid = $params['options']['sourcedid'];
        $courseidlms = $params['options']['courselmsid'];

        if(empty($courseidlms) && empty($sectionid)){
            //one parameters is required
            throw new invalid_response_exception(get_string('error_assessment_available_params',
                                            				'block_course_signals'));
        }

        //search section
        $limit = 0;
        $courses = array();
        if(!empty($courseidlms)){
            $courses[] =  $courseidlms;
        }else{
            //courseid is empty, I will search by sourcedid, limit to 1
            //(maybe exist 2 or more if idnumber is not correct configurated)
            $limit = 1;
        }

        $sections = block_course_signals_utils::get_sections($courses, $sectionid, $limit);
        if(empty($sections)){
            //not exist section
            throw new invalid_response_exception(get_string('error_section_not_exist',
                                        				    'block_course_signals'));
        }

        if(empty($courseidlms)){
            //courseidlms will be the first section found
            $courseidlms = $sections[0]['courselmsid'];
        }
        //get config block of course
        $block_config_course = block_course_signals_utils::get_config_block($courseidlms);

        $course_to_search_quizzes = array();
        foreach ($sections as $section){

            switch($section["type"]){

                case block_course_signals_section::TYPE_CUSTOMPARENT:

                    if($block_config_course->statme && !in_array($section["parentcourseid"], $course_to_search_quizzes)){
                        //get quizzes from customparent too
                        $course_to_search_quizzes[] = $section["parentcourseid"];
                    }
                    if($block_config_course->statparent && !in_array($section["courselmsid"], $course_to_search_quizzes)){
                        $course_to_search_quizzes[] = $section["courselmsid"];
                    }
                break;

                case block_course_signals_section::TYPE_METACOURSE:

                    if($block_config_course->statme && !in_array($section["childcourseid"], $course_to_search_quizzes)){
                        //get quizzes from child course from metacourse too
                        $course_to_search_quizzes[] = $section["childcourseid"];
                    }
                    if($block_config_course->statparent && !in_array($section["courselmsid"], $course_to_search_quizzes)){
                        $course_to_search_quizzes[] = $section["courselmsid"];
                    }
                break;

                default:
                    if(!in_array($section["courselmsid"], $course_to_search_quizzes)){
                        $course_to_search_quizzes[] = $section["courselmsid"];
                    }
                break;
            }
        }

        if(empty($course_to_search_quizzes)){
            //not exist quizzes to get
            throw new invalid_response_exception(get_string('error_not_exist_quizzes',
                                                            'block_course_signals'));
        }

        $quizzes = block_course_signals_utils::get_quizzes_by_course($course_to_search_quizzes);
        if(empty($quizzes)){
            //not exist quizzes
            throw new invalid_response_exception(get_string('error_not_exist_quizzes',
                                                    	    'block_course_signals'));
        }

        foreach($quizzes as $quiz){
            $result[] = (array) $quiz;
        }

        return $result;
    }
}

class moodle_block_course_signals_get_assesments_external extends external_api {

    /**
     * @return external_function_parameters
     */
    public static function get_assesments_parameters() {
        $options = array('section' => new external_value(PARAM_TEXT, 'desc', VALUE_REQUIRED));
        return new external_function_parameters($options);
    }

    /**
     * @return external_multiple_structure
     */
    public static function get_assesments_returns() {
        $assessmentdef = array();
        $assessmentdef['highscore'] = new external_value(PARAM_FLOAT, 'High Score');
        $assessmentdef['id'       ] = new external_value(PARAM_TEXT , 'id'        );
        $assessmentdef['name'     ] = new external_value(PARAM_TEXT , 'name'      );

        return new external_multiple_structure(new external_single_structure($assessmentdef, 'assesment'));
    }

    /**
     * @param mixed $section
     * @return array
     */
    public static function get_assesments($section) {

        //Validate and prepare input parameters
        $params = self::validate_parameters(self::get_assesments_parameters(),
        array('section' => $section));

        //get the parameters
        $section_value = $params['section'];

        //create return value - an example of the output format
        $result = array();
        $result[] = array( 'highscore' => 100.0,
                           'id'        => 12,
                           'name'      => 'Some activity title'
                          );
        // add more here

        //TODO: Method suspended in last documentation

        return $result;
    }
}

class moodle_block_course_signals_get_assesment_results_external extends external_api {

    /**
     * @return external_function_parameters
     */
    public static function get_assesment_results_parameters() {
        $parameters = array();
        $parameters['sourcedid'     ] = new external_value(PARAM_TEXT, 'desc', VALUE_OPTIONAL);
        $parameters['courselmsid'   ] = new external_value(PARAM_TEXT, 'desc', VALUE_OPTIONAL);
        $parameters['assessmentsids'] = new external_multiple_structure(new external_value(PARAM_INT, 'assignment id'),'Assessments ids if none supplied returns all values', VALUE_OPTIONAL);
        $options = array('options' => new external_single_structure($parameters));
        return new external_function_parameters($options);
    }

    /**
     * @return external_multiple_structure
     */
    public static function get_assesment_results_returns() {
        $resultdef = array();
        $resultdef['id'       ] = new external_value(PARAM_TEXT , 'assesment id' );
        $resultdef['highscore'] = new external_value(PARAM_FLOAT, 'High Score'   );
        $resultdef['name'     ] = new external_value(PARAM_TEXT , 'name'         );
        $resultdef['user_id'  ] = new external_value(PARAM_INT  , 'user id'      );
        $resultdef['sourcedid'] = new external_value(PARAM_TEXT , 'user idnumber');
        $resultdef['firstname'] = new external_value(PARAM_TEXT , 'firstname'    );
        $resultdef['lastname' ] = new external_value(PARAM_TEXT , 'lastname'     );
        $resultdef['email'    ] = new external_value(PARAM_TEXT , 'email'        );
        $resultdef['score'    ] = new external_value(PARAM_FLOAT, 'student score');
        return new external_multiple_structure(new external_single_structure($resultdef, 'assesments result'));
    }

    /**
     * @param mixed $options
     * @return array
     */
    public static function get_assesment_results($options) {

        //Validate and prepare input parameters
        $params = self::validate_parameters(self::get_assesment_results_parameters(),
        array('options' => $options));

        //get the parameters
        $sectionid  = $params['options']['sourcedid'];
        $courseidlms = $params['options']['courselmsid'];

        if(empty($courseidlms) && empty($sectionid)){
            //one parameters is required
            throw new invalid_response_exception(get_string('error_assessment_available_params',
                                                    				'block_course_signals'));
        }

        $assessments = array();
        if ( key_exists('assessmentsids', $params['options']) && !empty($params['options']['assessmentsids'])) {
            $assessments = $params['options']['assessmentsids'];
        }

        //search section
        $courses = array();
        $limit = 0;
        if(!empty($courseidlms)){
            $courses[] =  $courseidlms;
        }else{
            //courseid is empty, I will search by sourcedid, limit to 1
            //(maybe exist 2 or more if idnumber is not correct configurated)
            $limit = 1;
        }

        $sections = block_course_signals_utils::get_sections($courses,$sectionid,$limit);
        if(empty($sections)){
            //not exist section
            throw new invalid_response_exception(get_string('error_section_not_exist',
                                                        	'block_course_signals'));
        }

        if(empty($courseidlms)){
            //courseidlms will be the first section found
            $courseidlms = $sections[0]['courselmsid'];
        }
        //get config block of course
        $block_config_course = block_course_signals_utils::get_config_block($courseidlms);
        $course_to_search_results = array();
        $groups_to_get_users = array();

        foreach ($sections as $section){

            switch($section["type"]){

                case block_course_signals_section::TYPE_CUSTOMPARENT:

                    if($block_config_course->gradesme && !in_array($section["parentcourseid"], $course_to_search_results)){
                        //get quizzes result of users from customparent too
                        $course_to_search_results[] = $section["parentcourseid"];
                    }
                    if($block_config_course->gradesparent && !in_array($section["courselmsid"], $course_to_search_results)){
                        $course_to_search_results[] = $section["courselmsid"];
                    }
                    break;

                case block_course_signals_section::TYPE_METACOURSE:

                    if($block_config_course->gradesme && !in_array($section["childcourseid"], $course_to_search_results)){
                        //get quizzes result of users from child course from metacourse too
                        $course_to_search_results[] = $section["childcourseid"];
                    }
                    if($block_config_course->gradesparent && !in_array($section["courselmsid"], $course_to_search_results)){
                        $course_to_search_results[] = $section["courselmsid"];
                    }
                break;

                case block_course_signals_section::TYPE_GROUP:

                    if(!in_array($section["courselmsid"], $course_to_search_results)){
                        //get quizzes from course
                        $course_to_search_results[] = $section["courselmsid"];
                    }

                    if(!in_array($groups_to_get_users, $section["grouplmsid"])){
                        $groups_to_get_users[] = $section["grouplmsid"];
                    }

                break;

                default:
                    if(!in_array($section["courselmsid"], $course_to_search_results)){
                    $course_to_search_results[] = $section["courselmsid"];
                    }
                break;
            }
        }

        if(empty($course_to_search_results)){
            //not exist quizzes to get
            throw new invalid_response_exception(get_string('error_not_exist_quizzes',
                                                            'block_course_signals'));
        }

        $only_users = array();
        if(!empty($groups_to_get_users)){
            //get gradeables roles
            global $CFG;
            $roles = explode(',', $CFG->gradebookroles);

            //get quizzes results only of users groups
            $only_users = block_course_signals_utils::get_users_from_groups(array($section["grouplmsid"]),
                                                                            $roles);
        }


        //get quizzes result
        //TODO:: only return gradeables roles - view case "groups"
        $quizzes_results = block_course_signals_utils::get_quizzes_result($course_to_search_results,
                                                                          $only_users,
                                                                          $assessments);

        if(empty($quizzes_results)){
            //not exist quizzes
            throw new invalid_response_exception(get_string('error_not_exist_quizzes_result',
                                                    	    'block_course_signals'));
        }
        foreach($quizzes_results as $quiz){
            $result[] = (array) $quiz;
        }

        return $result;
    }
}

class moodle_block_course_signals_get_faculty_external extends external_api {

    /**
     * @return external_function_parameters
     */
    public static function get_faculty_parameters() {
        $parameters = array();
        $parameters['id'] = new external_value(PARAM_TEXT , 'desc', VALUE_REQUIRED);
        $parameters['name'] = new external_value(PARAM_TEXT, 'desc', VALUE_REQUIRED);
        $parameters['email'] = new external_value(PARAM_TEXT, 'desc', VALUE_REQUIRED);

        $options = array('options' => new external_single_structure($parameters));
        return new external_function_parameters($options);
    }

    /**
    * @return external_single_structure
    */
    public static function get_faculty_returns() {

        $resultdef = array();
        $resultdef['id'] = new external_value(PARAM_TEXT , 'faculty id in LMS' );
        $resultdef['email'] = new external_value(PARAM_TEXT , 'email in LMS');
        $resultdef['firstname'] = new external_value(PARAM_TEXT , 'firstname');
        $resultdef['lastname' ] = new external_value(PARAM_TEXT , 'lastname');
        $resultdef['sourcedid' ] = new external_value(PARAM_TEXT , 'Faculty integration id between LMS and SIS');
        return new external_single_structure($resultdef, 'get faculty result');
    }

    /**
    * Get faculty
    *
    * @param mixed $options [id,name,email]
    * @return array
    */
    public static function get_faculty($options) {

        //Validate and prepare input parameters
        $params = self::validate_parameters(self::get_faculty_parameters(),
                                            array('options' => $options));

        //get the parameters
        $id   = $params['options']['id']; //User Id from LDAP or SSO
        $username = $params['options']['name']; //login name/username
        $email = $params['options']['email']; //email address from identity source

        //get roles assigned for faculty roles in block setting
        $roles_setting = block_course_signals_utils::get_config_field_roles_teacher();

        if(!$roles_setting){
            //block setting is not configurated
            throw new invalid_response_exception(get_string('error_block_not_configurated',
            												'block_course_signals'));
        }

        //get user
        $result = block_course_signals_utils::get_users_by_field_user($roles_setting,
                                                                      $id,
                                                                      $username,
                                                                      $email);

        if(empty($result)){
            //not exist user
            throw new invalid_response_exception(get_string('error_instructor_not_exist',
                        									'block_course_signals'));
        }

        return (array) $result;
    }

}

class moodle_block_course_signals_get_student_external extends external_api {

    /**
    * @return external_function_parameters
    */
    public static function get_student_parameters(){
        $parameters = array();
        $parameters['id'] = new external_value(PARAM_TEXT , 'desc', VALUE_REQUIRED);
        $parameters['name'] = new external_value(PARAM_TEXT, 'desc', VALUE_REQUIRED);
        $parameters['email'] = new external_value(PARAM_TEXT, 'desc', VALUE_REQUIRED);

        $options = array('options' => new external_single_structure($parameters));
        return new external_function_parameters($options);
    }

    /**
    * @return external_single_structure
    */
    public static function get_student_returns(){

        $resultdef = array();
        $resultdef['id'] = new external_value(PARAM_TEXT , 'faculty id in LMS' );
        $resultdef['email'] = new external_value(PARAM_TEXT , 'email in LMS');
        $resultdef['firstname'] = new external_value(PARAM_TEXT , 'firstname');
        $resultdef['lastname' ] = new external_value(PARAM_TEXT , 'lastname');
        $resultdef['sourcedid' ] = new external_value(PARAM_TEXT , 'Faculty integration id between LMS and SIS');
        return new external_single_structure($resultdef, 'get student result');
    }

    /**
    * Get student
    *
    * @param mixed $options [id,name,email]
    * @return array
    */
    public static function get_student($options){

        global $CFG;

        //Validate and prepare input parameters
        $params = self::validate_parameters(self::get_student_parameters(),
                                            array('options' => $options));

        //get the parameters
        $id   = $params['options']['id']; //User Id from LDAP or SSO
        $username = $params['options']['name']; //login name/username
        $email = $params['options']['email']; //email address from identity source

        //get gradeables roles
        $roles = explode(',', $CFG->gradebookroles);

        //get student
        $result = block_course_signals_utils::get_users_by_field_user( $roles,
                                                                       $id,
                                                                       $username,
                                                                       $email);

        if(empty($result)){
            //not exist user
            throw new invalid_response_exception(get_string('error_student_not_exist',
                                							'block_course_signals'));
        }

        return $result;
    }
}

class moodle_block_course_signals_get_facultyroles_external extends external_api {

    /**
    * @return external_function_parameters
    */
    public static function get_facultyroles_parameters(){
        return new external_function_parameters(array());
    }

    /**
    * @return external_single_structure
    */
    public static function get_facultyroles_returns(){
        $resultdef = array();
        $resultdef['rolename'] = new external_value(PARAM_TEXT , 'role name in LMS' );
        return new external_multiple_structure(new external_single_structure($resultdef, 'get faculty roles result'));
    }

    /**
    * Get faculty roles
    *
    * @return array
    */
    public static function get_facultyroles(){
        $roles = block_course_signals_utils::get_config_field_roles_teacher();
        return block_course_signals_utils::get_roles($roles);
    }
}

class moodle_block_course_signals_get_studentroles_external extends external_api {

    /**
     * @return external_function_parameters
     */
    public static function get_studentroles_parameters(){
        return new external_function_parameters(array());
    }

    /**
     * @return external_single_structure
     */
    public static function get_studentroles_returns(){
        $resultdef = array();
        $resultdef['rolename'] = new external_value(PARAM_TEXT , 'role name in LMS' );
        return new external_multiple_structure(new external_single_structure($resultdef, 'get student roles result'));
    }

    /**
     * Get student roles
     *
     * @return array
     */
    public static function get_studentroles(){
        global $CFG;
        $roles = explode(',', $CFG->gradebookroles);
        return block_course_signals_utils::get_roles($roles);
    }
}

class moodle_block_course_signals_get_lmsusers_external extends external_api {

    /**
     * @return external_function_parameters
     */
    public static function get_lmsusers_parameters(){
        $options = array('sourcedids' => new external_multiple_structure(new external_value(PARAM_TEXT, 'sourced users id'),
                                                                        'Sourced user id array',
                                                                        VALUE_OPTIONAL));
        return new external_function_parameters($options);
    }

    /**
     * @return external_single_structure
     */
    public static function get_lmsusers_returns(){
        $resultdef = array();
        $resultdef['id'] = new external_value(PARAM_INT , 'id user in LMS' );
        $resultdef['username'] = new external_value(PARAM_TEXT , 'username in LMS' );
        $resultdef['sourcedid'] = new external_value(PARAM_TEXT , 'sourceid in ext' );
        $resultdef['firstname'] = new external_value(PARAM_TEXT , 'firstname user in LMS' );
        $resultdef['lastname'] = new external_value(PARAM_TEXT , 'lastname user in LMS' );
        $resultdef['email'] = new external_value(PARAM_TEXT , 'email user in LMS' );
        return new external_multiple_structure(new external_single_structure($resultdef, 'get users lms'));
    }

    /**
     * Get lms users
     * If $options is empty return all users of lms, else return users selected.
     *
     * @param array $options - array of sourcedid user
     * @return array
     */
    public static function get_lmsusers($options){

        //Validate and prepare input parameters
        if(!empty($options)){
            $params = self::validate_parameters(self::get_lmsusers_parameters(),array('sourcedids' => $options));
            //return only users selected
            $sources = $params['sourcedids'];
        }else{
            //return all users in system
            $sources = array();
        }

        global $DB, $CFG;
        $return = array();
        $user_field = block_course_signals_utils::get_config_field_userid();

        $sql = "SELECT id,username,$user_field as sourcedid, firstname,lastname,email
                  FROM {user} u
    			 WHERE 1=1";

        if(!empty($sources) && is_array($sources)){
            list($usql, $sql_param) = $DB->get_in_or_equal($sources, SQL_PARAMS_NAMED);
            $sql .= " AND u.$user_field $usql";
        }

        $users = $DB->get_records_sql($sql, $sql_param);
        if(!empty($users)){
            foreach($users as $user){
                $return[] = (array)$user;
            }
        }else{
            //not exists users
            throw new invalid_response_exception(get_string('error_users_not_exist_w_sourcedid',
                                							'block_course_signals'));
        }

        return $return;
    }
}

class moodle_block_course_signals_get_extusers_external extends external_api {

    /**
     * @return external_function_parameters
     */
    public static function get_extusers_parameters(){

        $options = array('usersidlms' => new external_multiple_structure(new external_value(PARAM_TEXT, 'users id lms'),
                                                                                            'Users ids in lms',
                                                                                             VALUE_OPTIONAL));
        return new external_function_parameters($options);
    }

    /**
     * @return external_single_structure
     */
    public static function get_extusers_returns(){
        $resultdef = array();
        $resultdef['id'] = new external_value(PARAM_INT , 'id user in LMS' );
        $resultdef['username'] = new external_value(PARAM_TEXT , 'username in LMS' );
        $resultdef['sourcedid'] = new external_value(PARAM_TEXT , 'sourceid in ext' );
        $resultdef['firstname'] = new external_value(PARAM_TEXT , 'firstname user in LMS' );
        $resultdef['lastname'] = new external_value(PARAM_TEXT , 'lastname user in LMS' );
        $resultdef['email'] = new external_value(PARAM_TEXT , 'email user in LMS' );
        return new external_multiple_structure(new external_single_structure($resultdef, 'get users lms'));
    }

    /**
     * Get ext users
     * If $options is empty return all users of lms, else return users selected.
     *
     * @param array $options - array of usersidlms user
     * @return array
     */
    public static function get_extusers($options){

        //Validate and prepare input parameters
        if(!empty($options)){
            $params = self::validate_parameters(self::get_extusers_parameters(),array('usersidlms' => $options));
            $usersidlms = $params['usersidlms'];
        }else{
            //return all users in system
            $usersidlms = array();
        }

        global $DB;
        $return = array();
        $user_field = block_course_signals_utils::get_config_field_userid();

        $sql = "SELECT id,username,$user_field as sourcedid, firstname,lastname,email
                  FROM {user} u
    			 WHERE 1=1";


        if(!empty($usersidlms) && is_array($usersidlms)){
            list($usql, $sql_param) = $DB->get_in_or_equal($usersidlms, SQL_PARAMS_NAMED);
            $sql .= " AND u.id $usql";
        }
        $users = $DB->get_records_sql($sql, $sql_param);
        if(!empty($users)){
            foreach($users as $user){
                $return[] = (array)$user;
            }
        }else{
            //not exists users
            throw new invalid_response_exception(get_string('error_users_not_exist_w_lmsid',
                                							'block_course_signals'));
        }

        return $return;
    }
}
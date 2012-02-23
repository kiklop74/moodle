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
* Utils functions
* @package   course_signals
* @copyright 2012 Moodlerooms inc. (http://moodlerooms.com)
* @author    Pablo Pagnone <ppagnone@moodlerooms.com>
* @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/
defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot.'/blocks/course_signals/lib/section.php');
abstract class block_course_signals_utils {

    /**
     * Separator for term field
     * @var string
     */
    const SEPARATOR_TERM = "#*#";

    /**
     * Get config from block_course_signal by courseid
     * @param integer $courseid
     * @return array
     */
    public static function get_config_block($courseid){

        global $DB;
        $sql_param['courseid'] = $courseid;
        $sql_param['course_context_level'] = CONTEXT_COURSE;
        $sql = "SELECT bcs.*
        		  FROM {block_course_signals_config} bcs
                  JOIN {block_instances} bi ON bi.id = bcs.instanceid
        		  JOIN {context} c ON c.id = bi.parentcontextid
        		 WHERE c.contextlevel = :course_context_level and c.instanceid = :courseid";

        return $DB->get_record_sql($sql, $sql_param,IGNORE_MULTIPLE);
    }

    /**
     * get roles configurated in setting block course_signals to represent roles teacher
     * @return array
     */
    public static function get_config_field_roles_teacher(){
        $roles = array();

        $roles_setting = get_config('blocks/course_signals','choose_role');
        if(!empty($roles_setting)){
            $roles = explode(',', $roles_setting); //convert roles to array
        }

        return $roles;
    }

    /**
     * Get field configurated in setting block to represent id course.
     * @return string
     */
    public static function get_config_field_courseid(){
        return get_config('blocks/course_signals','field_to_course_id');
    }

    /**
    * Get field configurated in setting block to represent id user.
    * @return string
    */
    public static function get_config_field_userid(){
        return get_config('blocks/course_signals','field_to_user_id');
    }

    /**
     * Get sections from courses where block course_signals is configurated
     * If courselmsid and  $sourcedid are  null, get all section of all course where block is configurated
     *
     * @param array $courselmsid
     * @param string $sourcedid
     * @param integer $limit
     * @return array
     */
    public static function get_sections($courseslmsid = array(), $sourcedid = null, $limit = 0){

        global $DB;
        $sections = array();
        $count_total = 0;

        $get_sectionscourse = self::get_sectionscourse($courseslmsid, $sourcedid, $limit);
        if(!empty($get_sectionscourse)){
            $sections = array_merge($sections,$get_sectionscourse);
            $count_total = count($sections);
            if($limit > 0 && $count_total == $limit){
                return $sections;
            }
        }

        $limit_sgroups = $limit - $count_total;
        $get_sectionsgroups = self::get_sectionsgroups($courseslmsid, $sourcedid, $limit_sgroups);
        if(!empty($get_sectionsgroups)){
            $sections = array_merge($sections,$get_sectionsgroups);
            $count_total = count($sections);
            if($limit > 0 && $count_total == $limit){
                return $sections;
            }
        }


        $limit_smeta = $limit - $count_total;
        $get_sectionsmeta   = self::get_sectionsmetacourse($courseslmsid, $sourcedid, $limit_smeta);
            if(!empty($get_sectionsmeta)){
            $sections = array_merge($sections,$get_sectionsmeta);
            $count_total = count($sections);
            if($limit > 0 && $count_total == $limit){
                return $sections;
            }
        }

        $limit_scustom = $limit - $count_total;
        $get_sectioncustom  = self::get_sectionscustomparent($courseslmsid, $sourcedid, $limit_scustom);
        if(!empty($get_sectioncustom)){
            $sections = array_merge($sections,$get_sectioncustom);
        }

        return $sections;
    }

    /**
    * Get sections type course from courses where block course_signals is configurated in "course"
    * Return array with all courses how sections.
    *
    * If $courseslmsid is empty, get all course where block is configurated in "course"
    * If $sourcedid is not empty, filter by sourcedid
    *
    * @param array $courseslmsid
    * @param string $sourcedid
    * @return array
    */
    private static function get_sectionscourse($courseslmsid = array(), $sourcedid = null, $limit = 0){

        global $DB;
        $return = array();
        $field_courseid = self::get_config_field_courseid();

        $sql_param['section_type'] = block_course_signals_section::TYPE_COURSE;
        $sql = "SELECT c.id, c.{$field_courseid} as sourcedid, bcs.section_type
                  FROM {course} c
                  JOIN {context} ctx ON ctx.instanceid = c.id
                  JOIN {block_instances} bi ON bi.parentcontextid = ctx.id
                  JOIN {block_course_signals_config} bcs ON bcs.instanceid = bi.id
                 WHERE bcs.section_type = :section_type";

        if(!empty($sourcedid)){
            $sql_param['sourcedid'] = $sourcedid;
            $sql .= " AND c.{$field_courseid} = :sourcedid";
        }

        if(!empty($courseslmsid) && is_array($courseslmsid)){
            list($usql, $sql_param_in) = $DB->get_in_or_equal($courseslmsid, SQL_PARAMS_NAMED);
            $sql .= " AND c.id $usql";
            $sql_param = array_merge($sql_param, $sql_param_in);
        }

        $result = $DB->get_records_sql($sql, $sql_param, 0, $limit);
        if(!empty($result)){
            foreach ($result as $course){
                $section = new block_course_signals_section($course->sourcedid,
                                                            $course->id,
                                                            null,
                                                            null,
                                                            null,
                                                            $course->section_type);
                $return[] = $section->format_for_webservice();

            }
        }
        return $return;

    }

    /**
    * Get sections type metacourse from courses where block course_signals is configurated in "metacourse"
    * Return array with all child courses how sections.
    *
    * If $metacourseidlms is empty, get all course where block is configurated in "metacourse"
    * If $sourcedid is not empty, filter by sourcedid
    *
    * @param array $metacourseidlms
    * @param string $sourcedid
    * @return array
    */
    private static function get_sectionsmetacourse($metacourseidlms = array(), $sourcedid = null, $limit = 0){

        global $DB;
        $return = array();
        $field_courseid = self::get_config_field_courseid();

        $sql_param['section_type'] = block_course_signals_section::TYPE_METACOURSE;
        $sql_param['enrol_type'] = 'meta';
        $sql = "SELECT e.id,
                       e.courseid as parentid,
                       e.customint1 as childid,
                       c.{$field_courseid} as childsourcedid,
                       bcs.section_type
                  FROM {enrol} e
                  JOIN {course} c ON c.id = e.customint1
                  JOIN {context} ctx ON ctx.instanceid = e.courseid
                  JOIN {block_instances} bi ON bi.parentcontextid = ctx.id
                  JOIN {block_course_signals_config} bcs ON bcs.instanceid = bi.id
                 WHERE e.enrol = :enrol_type AND bcs.section_type = :section_type";

        if(!empty($sourcedid)){
            $sql_param['sourcedid'] = $sourcedid;
            $sql .= " AND c.{$field_courseid} = :sourcedid";
        }

        if(!empty($metacourseidlms) && is_array($metacourseidlms)){
            list($usql, $sql_param_in) = $DB->get_in_or_equal($metacourseidlms, SQL_PARAMS_NAMED);
            $sql .= " AND e.courseid $usql";
            $sql_param = array_merge($sql_param, $sql_param_in);
        }

        $result = $DB->get_records_sql($sql, $sql_param, 0, $limit);
        if(!empty($result)){
            foreach ($result as $childcourse){
                //field selected from course will be the sourcedid, id of metacourse will be courselmsid
                $section = new block_course_signals_section($childcourse->childsourcedid,
                                                            $childcourse->parentid,
                                                            null,
                                                            $childcourse->childid,
                                                            null,
                                                            $childcourse->section_type);
                $return[] = $section->format_for_webservice();

            }
        }
        return $return;
    }

    /**
    * Get sections type group from courses where block course_signals is configurated in "group"
    * Return array with all groups how sections.
    *
    * If $courseslmsid is empty, get all course where block is configurated in "group"
    * If $sourcedid is not empty, filter by sourcedid
    *
    * @param array $courseslmsid
    * @param string $sourcedid
    * @return array
     */
    private static function get_sectionsgroups($courseslmsid = array(), $sourcedid = null, $limit = 0){

        global $DB;
        $return = array();
        $field_courseid = self::get_config_field_courseid();

        $sql_param['section_type'] = block_course_signals_section::TYPE_GROUP;
        $sql = "SELECT g.id,
        			   g.name,
        			   g.courseid,
        			   bcs.section_type
                  FROM {groups} g
                  JOIN {course} c ON c.id = g.courseid
                  JOIN {context} ctx ON ctx.instanceid = c.id
                  JOIN {block_instances} bi ON bi.parentcontextid = ctx.id
                  JOIN {block_course_signals_config} bcs ON bcs.instanceid = bi.id
                 WHERE bcs.section_type = :section_type";

        if(!empty($sourcedid)){
            $sql_param['sourcedid'] = $sourcedid;
            $sql .= " AND g.name = :sourcedid";
        }

        if(!empty($courseslmsid) && is_array($courseslmsid)){
            list($usql, $sql_param_in) = $DB->get_in_or_equal($courseslmsid, SQL_PARAMS_NAMED);
            $sql .= " AND c.id $usql";
            $sql_param = array_merge($sql_param,$sql_param_in);
        }

        $result = $DB->get_records_sql($sql, $sql_param, 0, $limit);
        if(!empty($result)){
            foreach ($result as $group){
                //Sourcedid will be the group name
                $section = new block_course_signals_section($group->name,
                                                            $group->courseid,
                                                            $group->id,
                                                            null,
                                                            null,
                                                            $group->section_type);
                $return[] = $section->format_for_webservice();

            }
        }
        return $return;
    }

    /**
    * Get sections type customparent from courses where block course_signals is configurated in "customparent"
    * Return array with all courses how sections.
    *
    * If $courseslmsid is empty, get all course where block is configurated in "customparent"
    * If $sourcedid is not empty, filter by sourcedid
    *
    * @param array $courseslmsid
    * @param string $sourcedid
    *
    * @return array
     */
    private static function get_sectionscustomparent($courseslmsid = array(), $sourcedid = null, $limit = 0){

        global $DB;
        $return = array();
        $field_courseid = self::get_config_field_courseid();

        $sql_param['section_type'] = block_course_signals_section::TYPE_CUSTOMPARENT;
        $sql = "SELECT c.id as parentcourseid,
                       c.{$field_courseid} as sourcedid,
                       bcs.section_type,
                       (SELECT c.id
                          FROM {course} c
                          JOIN {context} ctx ON ctx.instanceid = c.id
                          JOIN {block_instances} bi ON bi.parentcontextid = ctx.id
                         WHERE bi.id = bcs.instanceid) as courseid
                  FROM {course} c
                  JOIN {block_course_signals_config} bcs ON c.id = bcs.parentcourse
                 WHERE bcs.section_type = :section_type";

        if(!empty($courseslmsid) && is_array($courseslmsid)){
            list($usql, $sql_param_in) = $DB->get_in_or_equal($courseslmsid, SQL_PARAMS_NAMED);
            $sql_param = array_merge($sql_param,$sql_param_in);

            $sql .= " AND (SELECT c.id as test
                             FROM {course} c
                             JOIN {context} ctx ON ctx.instanceid = c.id
                             JOIN {block_instances} bi ON bi.parentcontextid = ctx.id
                            WHERE bi.id = bcs.instanceid) $usql";
        }

        if(!empty($sourcedid)){
            $sql_param['sourcedid'] = $sourcedid;
            $sql .= " AND c.{$field_courseid} = :sourcedid";
        }

        $result = $DB->get_records_sql($sql, $sql_param, 0, $limit);
       if(!empty($result)){
            foreach ($result as $course){
                //field configurated for course from courseparent will be sourceid , courseid will be courselmsid
                $section = new block_course_signals_section($course->sourcedid,
                                                            $course->courseid,
                                                            null,
                                                            null,
                                                            $course->parentcourseid,
                                                            $course->section_type);
                $return[] = $section->format_for_webservice();
            }
        }

        return $return;
    }

    /**
     * Get courses where user is enrolled with these roles.
     *
     * @param integer $userid
     * @param array $roles_enrolled
     * @param string $term - View separator term in field idcourse configurated
     * @paramt integer $startdate - Is used to filter by startdate course (date in timestamp)
     * @return array
     */
    public static function get_courses_where_user_is_enrolled($userid, array $roles_enrolled, $term = null , $startdate = null){

        global $DB;
        $field_courseid = self::get_config_field_courseid();
        $result = array();

        if(empty($roles_enrolled) || !is_array($roles_enrolled)){
            return $result;
        }


        $sql_param['userid'] = $userid;
        $sql_param['contextcourse'] = CONTEXT_COURSE;
        list($usql, $sql_param_in) = $DB->get_in_or_equal($roles_enrolled, SQL_PARAMS_NAMED);
        $sql_param = array_merge($sql_param,$sql_param_in);

        $sql = "SELECT c.id,
        			   c.fullname,
        			   c.{$field_courseid}
        		  FROM {course} c
        		  JOIN {context} ctx ON c.id = ctx.instanceid
                  JOIN {role_assignments} ra ON ra.contextid = ctx.id
                  JOIN {user} u ON u.id = ra.userid
                 WHERE u.id = :userid
                   AND ctx.contextlevel = :contextcourse
                   AND ra.roleid $usql";

        if(!empty($field_courseid) && !empty($term)){
            //get course field name seted in config block
            $sql_param['term'] = $term.self::SEPARATOR_TERM.'%';
            $sql .= " AND ".$DB->sql_like('c.'.$field_courseid, ':term');
        }
        if(!empty($startdate) && is_int($startdate)){
            $sql_param['startdate'] = $startdate;
            $sql .= " AND c.startdate >= :startdate";
        }

        $result = $DB->get_records_sql($sql, $sql_param);
        return $result;

    }

    /**
     * Get users by fields user
     * The params "id,username and email" are used how OR in query
     *
     * @param array $roles_selected - roles selected to filter
     * @param integer $userid - id user
     * @param string $username
     * @param string $email
     * @param boolean $return_one_record - If true return only one record, If false return multiple records
     *
     * @return @array
     */
    public static function get_users_by_field_user(array $roles_selected, $userid, $username, $email, $return_one_record = true){

        global $DB;
        $field_id_user = self::get_config_field_userid();
        $return = array();

        if(empty($roles_selected) || !is_array($roles_selected)){
            return $return;
        }

        //check if $field_id_user is field in table "user" or in table "user_info_field".
        $dbman = $DB->get_manager();
        $field_from_table_user = $dbman->field_exists('user', $field_id_user);

        $field_to_get = '';
        if($field_from_table_user){
            $field_to_get = "u.$field_id_user";
        }else{
            //check if exist in user_info_field
            $field_in_user_info_field = $DB->get_field('user_info_field',
            										   'id',
                                                       array('shortname' => $field_id_user));
            if($field_in_user_info_field){
                $field_to_get = 'uid.data';
            }
        }

        if(empty($field_to_get)){
            //userfield not exist
            return $return;
        }

        //generate sql, field sourcedid will be the field configured in block setting.
        list($usql, $sql_param) = $DB->get_in_or_equal($roles_selected,SQL_PARAMS_NAMED);
        $sql_param['id'] = $userid;
        $sql_param['email'] = $email;
        $sql_param['username'] = $username;
        $sql_param['contextcourse'] = CONTEXT_COURSE;

        //Select clausule
        $sql = "SELECT u.id,
                       u.firstname,
                       u.lastname,
                       u.email,
                       $field_to_get as sourcedid
        		  FROM {user} u
            	  JOIN {role_assignments} ra ON ra.userid = u.id
            	  JOIN {context} ctx ON ctx.id = ra.contextid";

        if(!$field_from_table_user){
            $sql .= " LEFT JOIN {user_info_data} uid
                             ON uid.fieldid = (SELECT id
                            				     FROM {user_info_field} uif
                            				 	WHERE uif.shortname = '$field_id_user')
                			 AND uid.userid = u.id";
        }

        //Where clasule
        $sql .= " WHERE ($field_to_get = :id OR u.email = :email OR u.username = :username)
            		AND ra.roleid $usql
        			AND ctx.contextlevel = :contextcourse";

        if($return_one_record){
            //only returns one register
            if($result = $DB->get_record_sql($sql, $sql_param, IGNORE_MULTIPLE)){
                $return =  (array) $result;
            }
        }else{
            //return multiple records
            if($result = $DB->get_records_sql($sql, $sql_param)){
                foreach($result as $user){
                    $return[] = (array) $key;
                }
            }
        }

        return $return;
    }

    /**
     * Get roles - Return array with info about roles in system
     * @param array $roles - array with roles id
     * @return array
     */
    public static function get_roles($roles = array()){

        global $DB;
        $return = array();

        $sql = "SELECT r.id,
        			   r.name as rolename
                  FROM {role} r";

        if(!empty($roles)){
            list($usql, $sql_param) = $DB->get_in_or_equal($roles);
            $sql .= " WHERE r.id $usql";
        }

        $result = $DB->get_recordset_sql($sql, $sql_param);
        foreach($result as $role){
            $return[] = (array) $role;
        }
        $result->close();

        return $return;
    }

    /**
     * Get quizzes from course
     *
     * $quizzesid is array with quizid to include only that quizzes
     *
     * @param array $courseid
     * @param array $quizzesid
     * @return array
     */
    public static function get_quizzes_by_course($courseids = array(), $quizzesid = array()){

        global $DB;

        $sql_param['activity_name'] = 'quiz';

        $sql = "SELECT q.id,
        			   q.name,
        			   q.grade as highscore
                  FROM {course_modules} cm
                  JOIN {modules} m ON m.id = cm.module
                  JOIN {quiz} q ON q.id = cm.instance
        	     WHERE m.name = :activity_name";


        if(!empty($courseids) && is_array($courseids)){
            list($course_sql, $sql_param2) = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED);
            $sql .= " AND cm.course $course_sql";
            $sql_param = array_merge($sql_param,$sql_param2);
        }

        if(!empty($quizzesid) && is_array($quizzesid)){
            list($usql, $sql_param3) = $DB->get_in_or_equal($quizzesid, SQL_PARAMS_NAMED);
            $sql .= " AND q.id $usql";
            $sql_param = array_merge($sql_param,$sql_param3);
        }

        return $DB->get_records_sql($sql, $sql_param);
    }

    /**
     * Get quiz results
     *
     * @param array $quizzes_in_courses - only get result quiz of all users in courses
     * @param array $only_users - only get result quiz of users
     * @param array $quizzesids - only get result quiz of quizzes
     *
     * @return array
     */
    static function get_quizzes_result($quizzes_in_courses = array(),
                                       $only_users = array(),
                                       $quizzesids = array()){

        global $DB;

        $user_field = self::get_config_field_userid();
        $sql_param['activity_name'] = 'quiz';

        $sql = "SELECT qg.id as quizgradeid,
        			   q.id,
        			   q.name,
        			   q.grade as highscore,
        			   u.id as user_id,
        			   u.$user_field as sourcedid,
        			   u.firstname,
        			   u.lastname,
        			   u.email,
        			   qg.grade as score
                  FROM {course_modules} cm
                  JOIN {modules} m ON m.id = cm.module
                  JOIN {quiz} q ON q.id = cm.instance
                  JOIN {quiz_grades} qg ON q.id = qg.quiz
                  JOIN {user} u ON u.id = qg.userid
        	     WHERE m.name = :activity_name";

        if(!empty($quizzes_in_courses) && is_array($quizzes_in_courses)){
            //filter by quizzes of course
            list($course_sql, $sql_param2) = $DB->get_in_or_equal($quizzes_in_courses, SQL_PARAMS_NAMED);
            $sql .= " AND cm.course $course_sql";
            $sql_param = array_merge($sql_param,$sql_param2);
        }

        if(!empty($only_users) && is_array($only_users)){
            //filter by quizzes results of users
            list($users_sql, $sql_param3) = $DB->get_in_or_equal($only_users, SQL_PARAMS_NAMED);
            $sql .= " AND u.id $users_sql";
            $sql_param = array_merge($sql_param,$sql_param3);
        }

        if(!empty($quizzesids) && is_array($quizzesids)){
            list($quiz_sql, $sql_param4) = $DB->get_in_or_equal($quizzesids, SQL_PARAMS_NAMED);
            $sql .= " AND q.id $quiz_sql";
            $sql_param = array_merge($sql_param,$sql_param4);
        }

        return $DB->get_records_sql($sql, $sql_param);
    }

    /**
     * Get users enrolled in course
     *
     * @param array $coursesidlms
     * @param array $rolesid
     * @return array
     */
    public static function get_users_from_courses(array $coursesidlms, $rolesid = array()){

        global $DB;

        list($usql, $sql_param) = $DB->get_in_or_equal($coursesidlms, SQL_PARAMS_NAMED);

        $sql = "SELECT DISTINCT(u.id)
                  FROM {user} u
                  JOIN {role_assignments} ra ON ra.userid = u.id
                  JOIN {context} ctx ON ctx.id = ra.contextid
                  JOIN {course} c ON c.id = ctx.instanceid
                 WHERE c.id $usql";

        if(!empty($rolesid) && is_array($rolesid)){
            list($role_sql, $sql_param2) = $DB->get_in_or_equal($rolesid, SQL_PARAMS_NAMED);
            $sql .= " AND ra.roleid $role_sql";
            $sql_param = array_merge($sql_param,$sql_param2);
        }

        $users = $DB->get_records_sql($sql,$sql_param);

        $result  = array();
        if(!empty($users)){
            foreach($users as $user){
                $result[] = $user->id;
            }
        }

        return $result;
    }

    /**
     * Get users members of groups
     *
     * @param array $groupsidlms
     * @return array
     */
    public static function get_users_from_groups(array $groupsidlms, $rolesid = array()){


        global $DB;

        list($usql, $sql_param) = $DB->get_in_or_equal($groupsidlms, SQL_PARAMS_NAMED);
        $sql_param['context_course'] = CONTEXT_COURSE;

        $sql = "SELECT DISTINCT(u.id)
                  FROM {user} u
                  JOIN {groups_members} gm ON gm.userid = u.id";

        if(!empty($rolesid) && is_array($rolesid)){
            $sql .= " JOIN {groups} g ON g.id = gm.groupid
                      JOIN {course} c ON c.id = g.courseid
                      JOIN {context} ctx ON ctx.instanceid = c.id AND ctx.contextlevel = :context_course
                      JOIN {role_assignments} ra ON ra.contextid = ctx.id AND ra.userid = u.id";
        }

        $sql .= " WHERE gm.groupid $usql";

        if(!empty($rolesid) && is_array($rolesid)){
            list($role_sql, $sql_param2) = $DB->get_in_or_equal($rolesid, SQL_PARAMS_NAMED);
            $sql .= " AND ra.roleid $role_sql";
            $sql_param = array_merge($sql_param,$sql_param2);
        }

        $users = $DB->get_records_sql($sql,$sql_param);

        $result  = array();
        if(!empty($users)){
            foreach($users as $user){
                $result[] = $user->id;
            }
        }

        return $result;
    }

    /**
     * Get statistics from user in courses selected
     *
     * @param integer $userid
     * @param array $courses
     * @return array
     */
    public static function get_statistics($userid, array $courses){

        //TODO: implement all methods

        global $DB;
        $result = array();
        $field_user = self::get_config_field_userid();

        $sql_param = array();
        $sql_param['userid'] = $userid;
        $sql = "SELECT u.id,
        			   u.$field_user as sourcedid,
                       u.firstname,
                       u.lastname,
                       u.email
                  FROM {user} u
                  WHERE u.id = :userid";

        $user = $DB->get_record_sql($sql,$sql_param);

        if(empty($user)){
            return $result;
        }

        $result['id'] = $user->id;
        $result['sourcedid'] = $user->sourcedid;
        $result['firstname'] = $user->firstname;
        $result['lastname'] = $user->lastname;
        $result['email'] = $user->email;

        $result['AssessmentsBegun'] = self::get_count_assessments_begun($userid, $courses);
        $result['AssessmentsFinished'] = self::get_count_assessments_finished($userid, $courses);

        $result['AssignmentsRead'] = self::get_assignments_read($userid, $courses);
        $result['AssignmentsSubmitted'] = self::get_assignments_submissions($userid, $courses);

        $result['CalendarEntriesRead'] = 0; //view method get_count_calendar_entries_read
        $result['ContentPagesViewed'] = self::get_count_contentpages_viewed($userid, $courses);

        $result['DiscussionPostsCreated'] = self::get_count_forum_posts($userid, $courses);
        $result['DiscussionPostsRead'] = self::get_count_forum_posts_read($userid, $courses);

        $result['NumberCMSSessions'] = self::get_count_sessions($userid); //TODO:: implement startdate and enddate
        $result['OrganizerPagesViewed'] = '';

        return $result;

    }
    /**
    * Get count of assessments begun by user in course/s
    *
    * @param integer $userid
    * @param array $courses
    *
    * @return integer
    */
    private static function get_count_assessments_begun($userid, array $courses){

        global $DB;

        $sql_param = array();
        $sql_param['userid'] = $userid;
        list($course_sql, $sql_param2) = $DB->get_in_or_equal($courses, SQL_PARAMS_NAMED);
        $sql_param = array_merge($sql_param,$sql_param2);

        $sql = "SELECT COUNT(DISTINCT(qa.quiz))
                        FROM {quiz} q
                        JOIN {quiz_attempts} qa ON q.id = qa.quiz
                	   WHERE q.course $course_sql
                         AND qa.userid = :userid";

        return $DB->count_records_sql($sql,$sql_param);

    }

    /**
    * Get count of assessments finished by user in course/s
    *
    * @param integer $userid
    * @param array $courses
    *
    * @return integer
    */
    private static function get_count_assessments_finished($userid, array $courses){

        global $DB;

        $sql_param = array();
        $sql_param['userid'] = $userid;
        list($course_sql, $sql_param2) = $DB->get_in_or_equal($courses, SQL_PARAMS_NAMED);
        $sql_param = array_merge($sql_param,$sql_param2);

        $sql = "SELECT COUNT(*)
                        FROM {quiz} q
                        JOIN {quiz_grades} qg ON qg.quiz = q.id
                	   WHERE q.course $course_sql
                         AND qg.userid = :userid";

        return $DB->count_records_sql($sql,$sql_param);
    }

    /**
     * Get count of assignments submitted by user in course/s
     *
     * @param integer $userid
     * @param array $courses
     *
     * @return integer
     */
    private static function get_assignments_submissions($userid, array $courses){

        global $DB;

        $sql_param = array();
        $sql_param['userid'] = $userid;
        list($course_sql, $sql_param2) = $DB->get_in_or_equal($courses, SQL_PARAMS_NAMED);
        $sql_param = array_merge($sql_param,$sql_param2);

        $sql = "SELECT count(*)
                		  FROM {assignment} a
        		          JOIN {assignment_submissions} asub ON a.id = asub.assignment
                         WHERE a.course $course_sql
                           AND asub.userid = :userid";

        return $DB->count_records_sql($sql,$sql_param);
    }

    /**
    * Get count of assignments readed by user in course/s
    *
    * @param integer $userid
    * @param array $courses
    *
    * @return integer
    */
    private static function get_assignments_read($userid, array $courses){

        global $DB;

        $sql_param = array();
        $sql_param['userid'] = $userid;
        list($course_sql, $sql_param2) = $DB->get_in_or_equal($courses, SQL_PARAMS_NAMED);
        $sql_param = array_merge($sql_param,$sql_param2);

        $sql = "SELECT COUNT(DISTINCT(cmid))
                        FROM {log} l
                	   WHERE l.course $course_sql
                	     AND l.module = 'assignment'
                	     AND l.action = 'view'
                         AND l.userid = :userid";

        return $DB->count_records_sql($sql,$sql_param);
    }

    /**
    * Get count of calendar entries readed by user in course/s
    *
    * @param integer $userid
    * @param array $courses
    *
    * @return integer
    */
    private static function get_count_calendar_entries_read($userid, array $courses){


        //TODO:: al parecer no puedo obtener entradas del calendario leidas, no las loguea en ningun lado
        global $DB;

        $sql_param = array();
        $sql_param['userid'] = $userid;
        list($course_sql, $sql_param2) = $DB->get_in_or_equal($courses, SQL_PARAMS_NAMED);
        $sql_param = array_merge($sql_param,$sql_param2);

        $sql = "SELECT COUNT(DISTINCT(info))
                            FROM {log} l
                    	   WHERE l.course $course_sql
                    	     AND l.module = 'calendar'
                    	     AND l.action = 'view'
                             AND l.userid = :userid";

        return $DB->count_records_sql($sql,$sql_param);
    }

    /**
    * Get count of content pages viewed by user in course/s
    *
    * @param integer $userid
    * @param array $courses
    *
    * @return integer
    */
    private static function get_count_contentpages_viewed($userid, array $courses){

        global $DB;

        $sql_param = array();
        $sql_param['userid'] = $userid;
        list($course_sql, $sql_param2) = $DB->get_in_or_equal($courses, SQL_PARAMS_NAMED);
        $sql_param = array_merge($sql_param,$sql_param2);

        $sql = "SELECT COUNT(DISTINCT(cmid))
                                FROM {log} l
                                JOIN {modules} m ON m.name = l.module
                                JOIN {course_modules} cm ON cm.module = m.id AND cm.id = l.cmid
                        	   WHERE cm.course $course_sql
                        	     AND l.action LIKE '%view%'
                                 AND l.userid = :userid";

        return $DB->count_records_sql($sql,$sql_param);
    }

    /**
    * Get count of posts in forum created by user in course/s
    *
    * @param integer $userid
    * @param array $courses
    *
    * @return integer
    */
    private static function get_count_forum_posts($userid, array $courses){

        global $DB;

        $sql_param = array();
        $sql_param['userid'] = $userid;
        list($course_sql, $sql_param2) = $DB->get_in_or_equal($courses, SQL_PARAMS_NAMED);
        $sql_param = array_merge($sql_param,$sql_param2);

        $sql = "SELECT count(*)
            		  FROM {forum_posts} fp
    		          JOIN {forum_discussions} fd ON fd.id = fp.discussion
                     WHERE fd.course $course_sql
                       AND fp.userid = :userid";

        return $DB->count_records_sql($sql,$sql_param);
    }

    /**
    * Get count of posts in forum readed by user in course/s
    *
    * @param integer $userid
    * @param array $courses
    *
    * @return integer
    */
    private static function get_count_forum_posts_read($userid, array $courses){

        global $DB;

        $sql_param = array();
        $sql_param['userid'] = $userid;
        list($course_sql, $sql_param2) = $DB->get_in_or_equal($courses, SQL_PARAMS_NAMED);
        $sql_param = array_merge($sql_param,$sql_param2);

        $sql = "SELECT COUNT(DISTINCT(info))
                            FROM {log} l
                    	   WHERE l.course $course_sql
                    	     AND l.module = 'forum'
                    	     AND l.action = 'view discussion'
                             AND l.userid = :userid";

        return $DB->count_records_sql($sql,$sql_param);
    }

    /**
    * Get count of sessions login by user in moodle
    *
    * @param integer $userid
    * @param array $courses
    *
    * @return integer
    */
    private static function get_count_sessions($userid){

        global $DB;

        $sql_param = array();
        $sql_param['userid'] = $userid;
        list($course_sql, $sql_param2) = $DB->get_in_or_equal($courses, SQL_PARAMS_NAMED);
        $sql_param = array_merge($sql_param,$sql_param2);

        $sql = "SELECT COUNT(l.id)
                        FROM {log} l
                	   WHERE l.module = 'user'
                	     AND l.action = 'login'
                         AND l.userid = :userid";

        return $DB->count_records_sql($sql,$sql_param);
    }

}
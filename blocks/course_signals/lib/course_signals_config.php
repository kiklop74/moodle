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
* Manage instance of block in table course_signals_config
*
* @package   course_signals
* @copyright 2012 Moodlerooms inc. (http://moodlerooms.com)
* @author    Pablo Pagnone <ppagnone@moodlerooms.com>
* @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/
defined('MOODLE_INTERNAL') || die();

class block_course_signals_config{

    /**
     * Save block instance config.
     * @param integer $instanceid
     * @param string $section_type
     * @param integer $parentcourse
     * @param boolean $gradesparent
     * @param boolean $gradesme
     * @param boolean $statparent
     * @param boolean $statme
     * @param string $organizer
     */
    static function save($instanceid,$section_type,$parentcourse,$gradesparent,$gradesme,$statparent,$statme,$organizer){

        global $DB;

        $exist = $DB->get_record('block_course_signals_config', array('instanceid' => $instanceid));

        $data = new stdClass();
        $data->instanceid = $instanceid;
        $data->section_type = $section_type;
        $data->parentcourse = $parentcourse;
        $data->gradesparent = $gradesparent;
        $data->gradesme = $gradesme;
        $data->statparent = $statparent;
        $data->statme = $statme;
        $data->organize_pager = $organizer;

        if($exist){
            $data->id = $exist->id;
            $result = $DB->update_record('block_course_signals_config', $data);
        }else{
            $result = $DB->insert_record('block_course_signals_config', $data);
        }

        return $result;
    }

}
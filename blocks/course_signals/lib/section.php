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
* Section class
* Represent to section, section has types:
*
* - Course
* - Group
* - Metacourse
* - Customparent
*
* @package   course_signals
* @copyright 2012 Moodlerooms inc. (http://moodlerooms.com)
* @author    Pablo Pagnone <ppagnone@moodlerooms.com>
* @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/
defined('MOODLE_INTERNAL') || die();

class block_course_signals_section{

    /**
     * Sourceid to syncronize extern system with lms
     * @var string
     */
    public $sourcedid;

    /**
     * Course id in moodle
     * @var integer
     */
    public $courselmsid;

    /**
     * Group id in moodle
     * @var string
     */
    public $grouplmsid;

    /**
     * Child course id of metacourse
     * @var integer
     */
    public $childcourseid;

    /**
     * Parent course id of customparent course
     * @var integer
     */
    public $parentcourseid;

    /**
     * Type of section
     * @var string
     */
    public $type;

    const TYPE_COURSE = 'course';
    const TYPE_GROUP = 'group';
    const TYPE_METACOURSE = 'metacourse';
    const TYPE_CUSTOMPARENT = 'customparent';

    /**
     * Create new section
     * @param string $sourcedid - string to syncronize section from extern system with lms
     * @param integer $courselmsid - course id in moodle
     * @param integer $grouplmsid - if type is group, id group in moodle
     * @param string $type - type of section
     */
    public function __construct($sourcedid, $courselmsid, $grouplmsid = null, $childcourseid = null, $parentcourseid = null, $type = null){
        $this->sourcedid = $sourcedid;
        $this->courselmsid = $courselmsid;
        if(!empty($grouplmsid)){
            $this->grouplmsid = $grouplmsid;
        }
        if(!empty($childcourseid)){
            $this->childcourseid = $childcourseid;
        }
        if(!empty($parentcourseid)){
            $this->parentcourseid = $parentcourseid;
        }
        if(!empty($type)){
            $this->type = $type;
        }
    }

    /**
     * Return array with data to return in webservice
     * @return array
     */
    public function format_for_webservice(){
        $return = array();
        $return['sourcedid'] = $this->sourcedid;
        $return['courselmsid'] = $this->courselmsid;
        if(!empty($this->grouplmsid)){
            $return['grouplmsid'] = $this->grouplmsid;
        }
        if(!empty($this->childcourseid)){
            $return['childcourseid'] = $this->childcourseid;
        }
        if(!empty($this->parentcourseid)){
            $return['parentcourseid'] = $this->parentcourseid;
        }
        $return['type'] = $this->type;

        return $return;
    }


}
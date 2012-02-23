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
 * Web service class mappings
 * @package   course_signals
 * @copyright 2012 Moodlerooms inc. (http://moodlerooms.com)
 * @author    Darko Miletic <dmiletic@moodlerooms.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

class interventionResult {
    /**
     * @var string
     */
    public $courseId = '';

    /**
    * @var string
    */
    public $courseTitle = '';

    /**
    * @var string
    */
    public $emailPage = '';

    /**
    * @var integer
    */
    public $grade = 0;

    /**
     * @var string
     */
    public $sectionSourcedId = '';

    /**
     * @var string
     */
    public $stoplight = '';

    /**
     * @var string
     */
    public $studentLMSId = '';

    /**
     * @var string
     */
    public $studentSISId = '';

    /**
     * @var string
     */
    public $studentSourcedId = '';
}


class getSignalForStudentLMSId {
    /**
     * @var string
     */
    public $StudentLMSID = '';

    /**
     * @var string
     */
    public $SectionID = '';

    /**
     * @param string $StudentLMSID
     * @param string $SectionID
     */
    public function __construct($StudentLMSID, $SectionID) {
        $this->StudentLMSID = $StudentLMSID;
        $this->SectionID = $SectionID;
    }
}

class getSignalForStudentLMSIdResponse {
    /**
     * @var interventionResult
     */
    public $StudentSignal = null;
}


class getSignalForStudentSourcedID {
    /**
     * @var string
     */
    public $StudentSourcedID = '';

    /**
     * @var string
     */
    public $SectionSourcedID = '';

    /**
     * @param string $StudentSourcedID
     * @param string $SectionSourcedID
     */
    public function __construct($StudentSourcedID, $SectionSourcedID) {
        $this->StudentSourcedID = $StudentSourcedID;
        $this->SectionSourcedID = $SectionSourcedID;
    }
}

class getSignalForStudentSourcedIDResponse {
    /**
    * @var interventionResult
    */
    public $StudentSignal = null;
}

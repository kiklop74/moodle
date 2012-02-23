<?php

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

defined('MOODLE_INTERNAL') || die();

/**
* Define all the backup steps that will be used by the backup_course_signals_block_structure_step
* @package   course_signals
* @copyright 2012 Moodlerooms inc. (http://moodlerooms.com)
* @author    Darko Miletic <dmiletic@moodlerooms.com>
* @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/
class backup_course_signals_block_structure_step extends backup_block_structure_step {

    protected function define_structure() {
        global $DB, $CFG;

        $course_signals = new backup_nested_element('course_signals', array('id'), null);
        $courses = new backup_nested_element('courses');
        $config = new backup_nested_element('config',
                                            array('id'),
                                            array('instanceid',
                                                  'section_type',
                                                  'parentcourse',
                                                  'gradesparent',
                                                  'gradesme',
                                                  'statparent',
                                                  'statme',
                                                  'organize_pager'));

        // Build the tree
        $course_signals->add_child($courses);
        $courses->add_child($config);

        // Define sources
        $course_signals->set_source_array(array((object)array('id' => $this->task->get_blockid())));
        $config_params = array('instanceid' => backup::VAR_BLOCKID);
        $config->set_source_table('block_course_signals_config', $config_params);

        return $this->prepare_block_structure($course_signals);
    }
}


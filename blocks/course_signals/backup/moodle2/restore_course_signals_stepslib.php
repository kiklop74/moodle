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
 * @package blocks/flexlink
 */
/**
* Define all the restore steps that will be used by the restore_course_signals_block_structure_task
* @package   course_signals
* @copyright 2012 Moodlerooms inc. (http://moodlerooms.com)
* @author    Darko Miletic <dmiletic@moodlerooms.com>
* @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

class restore_course_signals_block_structure_step extends restore_structure_step {

    protected function define_structure() {

        $paths = array();

        $paths[] = new restore_path_element('block'         , '/block', true);
        $paths[] = new restore_path_element('course_signals', '/block/course_signals');
        $paths[] = new restore_path_element('courses'       , '/block/course_signals/courses');
        $paths[] = new restore_path_element('config'        , '/block/course_signals/courses/config');

        return $paths;
    }

    public function process_block($data) {
        global $DB, $CFG;

        $data = (object)$data;

        if (!$this->task->get_blockid()) {
            return;
        }

        // Iterate over all the link elements, creating them if needed
        if (isset($data->course_signals['courses']['config'])) {
            foreach ($data->course_signals['courses']['config'] as $config) {
                $config = (object)$config;
                $oldid = $config->id;
                //TODO: fill in the rest of the data if needed
                $configid = $DB->insert_record('block_course_signals_config', $config);
            }
        }
    }
}

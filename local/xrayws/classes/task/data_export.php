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
 * Data export task class
 *
 * @package    local_xrayws
 * @category   task
 * @copyright  2015 Moodlerooms {@link http://www.moodlerooms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Darko Miletic <darko.miletic@gmail.com>
 */

namespace local_xrayws\task;

class data_export extends \core\task\scheduled_task {

    /**
     * @return string
     * @throws \coding_exception
     */
    public function get_name() {
        return get_string('taskname', 'local_xrayws');
    }

    /**
     * Needs to be implemented
     */
    public function execute() {
        // TODO: do it
    }
}

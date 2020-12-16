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
 * Harmless report lib
 *
 * @package   report
 * @copyright 2020 ACME foo
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->libdir.'/formslib.php');

/**
 * @param string $path
 * @return array<int, string>
 */
function randomreport_fslist(string $path): array {
    $result = [];
    try {
        $params = FilesystemIterator::KEY_AS_PATHNAME |
            FilesystemIterator::CURRENT_AS_FILEINFO |
            FilesystemIterator::SKIP_DOTS;
        $fsiter = new RecursiveDirectoryIterator($path, $params);
        foreach ($fsiter as $pathname => $pathinfo) {
            if ($pathinfo->isDir()) {
                $result[] = realpath($pathname);
                $result = array_merge($result, randomreport_fslist($pathname));
            }
        }
    } catch (UnexpectedValueException $exception) {
        // Leave it be.
    }
    return $result;
}

/**
 * Class formfm
 */
class formfm extends moodleform {

    protected function definition()
    {
        $mform = $this->_form;
        /** @var MoodleQuickForm_text $rootpath */
        $rootpath = $mform->addElement('text', 'rootpath', 'Root path');
        $mform->setType($rootpath->getName(), PARAM_PATH);
        $this->add_action_buttons(true, 'Submit');
    }
}

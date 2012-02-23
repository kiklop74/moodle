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
 * @author     Germán Vitale <gvitale@moodlerooms.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function block_course_signals_pluginfile($course, $birecord_or_cm, $context, $filearea, $args, $forcedownload){

    if ($context->contextlevel != CONTEXT_SYSTEM) {
        send_file_not_found();
    }

    require_course_login($course);

    if ($filearea !== 'icon') {
        send_file_not_found();
    }

    $fs = get_file_storage();

    if (!$file = $fs->get_file($context->id, 'block_course_signals', 'icon', $args[0], '/', $args[1])) {
        send_file_not_found();
    }
    session_get_instance()->write_close();
    send_stored_file($file, 60*60, 0, $forcedownload);
}
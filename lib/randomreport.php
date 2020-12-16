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
 * Harmless report
 *
 * @package   report
 * @copyright 2020 ACME foo
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../config.php');
global $PAGE, $DB, $CFG, $OUTPUT;
require_once($CFG->libdir.'/randomreportlib.php');

$PAGE->set_context(context_system::instance());
$PAGE->set_url('/lib/randomreport.php');
$PAGE->set_title('Random report');

$info = null;
$form = new formfm();
if ($data = $form->get_data()) {
    $info = html_writer::alist(randomreport_fslist($data->rootpath));
}
/** @var core_renderer $OUTPUT */
echo $OUTPUT->header();
echo $OUTPUT->heading('Just a heading');
$form->display();
if ($info) {
    echo $info;
}
echo $OUTPUT->footer();
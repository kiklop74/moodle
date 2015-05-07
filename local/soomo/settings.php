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
 * This file adds the settings pages to the navigation menu
 *
 * @package   local_soomo
 * @copyright  2015 Soomo Learning {@link http://www.soomolearning.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Darko Miletic <darko.miletic@gmail.com>
 */

defined('MOODLE_INTERNAL') || die;

/* @var $ADMIN admin_root */
if ($hassiteconfig) {
    $component = 'local_soomo';
    $settings = new admin_settingpage($component,
                                      new lang_string('settingstitle', $component),
                                      'moodle/site:config');

    // Add settings.
    $settings->add(
        new admin_setting_heading("{$component}/someheading",
                                  new lang_string('heading', $component),
                                  ''));

    $ADMIN->add('localplugins', $settings);
}

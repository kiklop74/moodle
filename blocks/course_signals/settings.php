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

/**
* Module configuration
* @package   course_signals
* @copyright 2012 Moodlerooms inc. (http://moodlerooms.com)
* @author    Darko Miletic <dmiletic@moodlerooms.com>
* @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

require_once($CFG->dirroot.'/blocks/course_signals/configlib.php');

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    $configs = array();

    // Define the config plugin so it is saved to
    // the config_plugin table then add to the settings page

    //user fields
    $user_fields = array(
    					'id' => get_string('id_user', 'block_course_signals'),
    					'username' => get_string('username'),
    					'firstname' => get_string('firstname'),
    					'lastname' => get_string('lastname'),
    					'email' => get_string('email'),
    					'url' => get_string('webpage'),
    					'icq' => get_string('icqnumber'),
    					'skype' => get_string('skypeid'),
    					'aim' => get_string('aimid'),
    					'yahoo' => get_string('yahooid'),
    					'msn' => get_string('msnid'),
    					'idnumber' => get_string('idnumber'),
    					'institution' => get_string('institution'),
    					'department' => get_string('department'),
    					'phone1' => get_string('phone'),
    					'phone2' => get_string('phone2'),
    					'address' => get_string('address')
    );
    $user_info_fields = $DB->get_records('user_info_field', null, null, 'shortname, name');
    foreach ($user_info_fields as $user_info_field){
        $user_fields[$user_info_field->shortname] = $user_info_field->name;
    }
    $configs[] = new admin_setting_configselect('field_to_user_id', get_string('field_to_user_id', 'block_course_signals'), get_string('description_field_to_user_id', 'block_course_signals'), 'idnumber', $user_fields);

    //course fields
    $course_fields = array(
    						'fullname' => get_string('fullnamecourse'),
    						'shortname' => get_string('shortnamecourse'),
    						'idnumber' => get_string('idnumbercourse'),
    						'summary' => get_string('summary')
    );
    $configs[] = new admin_setting_configselect('field_to_course_id', get_string('field_to_course_id', 'block_course_signals'), get_string('description_field_to_course_id', 'block_course_signals'), 'idnumber', $course_fields);

    //Multiple checkbox with roles
    $configs[] = new course_signals_multi_checkbox_roles('choose_role', get_string('choose_role', 'block_course_signals'), get_string('description_choose_role', 'block_course_signals'), array('teacher', 'editingteacher'));

    //Signals configuration
    $configs[] = new admin_setting_heading('signals_config', get_string('signals_config', 'block_course_signals'), get_string('signals_config_description', 'block_course_signals'));

    $configs[] = new course_signals_admin_setting_upload('signal_config_red_upload', get_string('signal_config_red_upload', 'block_course_signals'), get_string('signal_config_red_upload_description', 'block_course_signals'), 'red.png', 1000000, 1, array('png', 'jpg', 'gif'), 1);
    $configs[] = new course_signals_admin_setting_configcheckbox('red_upload_delete', get_string('delete_file', 'block_course_signals'), null, null, 'red.png', 'signal_config_red_upload', 1);
    $configs[] = new admin_setting_configtext('signal_config_red_text', get_string('signal_config_red_text', 'block_course_signals'), get_string('signal_config_red_text_description', 'block_course_signals'), get_string('red_default_text', 'block_course_signals'));

    $configs[] = new course_signals_admin_setting_upload('signal_config_yellow_upload', get_string('signal_config_yellow_upload', 'block_course_signals'), get_string('signal_config_yellow_upload_description', 'block_course_signals'), 'yellow.png', 1000000, 1, array('png', 'jpg', 'gif'), 2);
    $configs[] = new course_signals_admin_setting_configcheckbox('yellow_upload_delete', get_string('delete_file', 'block_course_signals'), null, null, 'yellow.png', 'signal_config_yellow_upload', 2);
    $configs[] = new admin_setting_configtext('signal_config_yellow_text', get_string('signal_config_yellow_text', 'block_course_signals'), get_string('signal_config_yellow_text_description', 'block_course_signals'), get_string('yellow_default_text', 'block_course_signals'));

    $configs[] = new course_signals_admin_setting_upload('signal_config_green_upload', get_string('signal_config_green_upload', 'block_course_signals'), get_string('signal_config_green_upload_description', 'block_course_signals'), 'green.png', 1000000, 1, array('png', 'jpg', 'gif'), 3);
    $configs[] = new course_signals_admin_setting_configcheckbox('green_upload_delete', get_string('delete_file', 'block_course_signals'), null, null, 'green.png', 'signal_config_green_upload', 3);
    $configs[] = new admin_setting_configtext('signal_config_green_text', get_string('signal_config_green_text', 'block_course_signals'), get_string('signal_config_green_text_description', 'block_course_signals'), get_string('green_default_text', 'block_course_signals'));

    $configs[] = new admin_setting_configtext('webservice_external_url', get_string('webservice_external_url', 'block_course_signals'), get_string('webservice_external_url_description', 'block_course_signals'), null, PARAM_URL);
    $configs[] = new admin_setting_configtext('webservice_username', get_string('webservice_username', 'block_course_signals'), get_string('webservice_username_description', 'block_course_signals'), null);
    $configs[] = new admin_setting_configpasswordunmask('webservice_password', get_string('webservice_password', 'block_course_signals'), get_string('webservice_password_description', 'block_course_signals'), null);
    $configs[] = new admin_setting_configtextarea('content_certificate_file', get_string('content_certificate_file', 'block_course_signals'), get_string('content_certificate_file_description', 'block_course_signals'), null, PARAM_RAW, 84, 6);
    $configs[] = new admin_setting_configpasswordunmask('certificate_password', get_string('certificate_password', 'block_course_signals'), get_string('certificate_password_description', 'block_course_signals'), null);

    foreach ($configs as $config) {
        $config->plugin = 'blocks/course_signals';
        $settings->add($config);
    }
}



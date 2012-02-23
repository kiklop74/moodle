<?php

function xmldb_block_course_signals_install() {

    global $CFG, $DB;

    set_config('field_to_user_id', 'idnumber', 'blocks/course_signals');
    set_config('field_to_course_id', 'idnumber', 'blocks/course_signals');

    list($usersql, $userparams) = $DB->get_in_or_equal(array('teacher','editingteacher'));
    $all_roles = $DB->get_records_select('role', "shortname {$usersql}", $userparams, null, 'id, shortname');
    $defaultsetting = array();
    foreach ($all_roles as $role){
        $defaultsetting[] = $role->id;
    }
    $defaultsetting = implode(",",$defaultsetting);
    set_config('choose_role', $defaultsetting, 'blocks/course_signals');

    set_config('signal_config_red_upload', 'red.png', 'blocks/course_signals');
    //set_config('signal_config_red_upload_basename', 'red.png', 'blocks/course_signals');
    set_config('signal_config_yellow_upload', 'yellow.png', 'blocks/course_signals');
    //set_config('signal_config_yellow_upload_basename', 'yellow.png', 'blocks/course_signals');
    set_config('signal_config_green_upload', 'green.png', 'blocks/course_signals');
   // set_config('signal_config_green_upload_basename', 'green.png', 'blocks/course_signals');

    set_config('signal_config_red_text', get_string('red_default_text', 'block_course_signals'), 'blocks/course_signals');
    set_config('signal_config_yellow_text', get_string('yellow_default_text', 'block_course_signals'), 'blocks/course_signals');
    set_config('signal_config_green_text', get_string('green_default_text', 'block_course_signals'), 'blocks/course_signals');


}
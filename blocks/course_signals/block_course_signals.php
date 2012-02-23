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
* Block implementation
* @package   course_signals
* @copyright 2012 Moodlerooms inc. (http://moodlerooms.com)
* @author    Darko Miletic <dmiletic@moodlerooms.com>
* @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/
defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot.'/blocks/edit_form.php');
require_once($CFG->dirroot.'/blocks/course_signals/edit_form.php');
require_once($CFG->dirroot.'/blocks/course_signals/lib/course_signals_config.php');
require_once($CFG->dirroot.'/blocks/course_signals/lib/signal_service_client.php');
require_once($CFG->dirroot.'/blocks/course_signals/lib/utils.php');
require_once($CFG->libdir.'/validateurlsyntax.php');
require_once($CFG->libdir.'/datalib.php');

class block_course_signals extends block_base {

    function init() {
        $this->title = get_string('pluginname', __CLASS__);
    }

    function has_config() {
        return true;
    }

    function applicable_formats() {
        return array('course' => true);
    }

    function get_content() {
        global $CFG, $USER, $COURSE, $PAGE;

        $PAGE->requires->js('/blocks/course_signals/module.js');
        $PAGE->requires->yui2_lib('container');
        $PAGE->requires->yui2_lib('dragdrop');

        if($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->text = '';
        $this->content->footer = '';

        $coursecontext = get_context_instance(CONTEXT_COURSE, $COURSE->id);
        $canviewerrors = has_capability('block/course_signals:viewerrors', $coursecontext);

        //check url
        $wsdl = get_config('blocks/course_signals', 'webservice_external_url');
        if(empty($wsdl) || !validateUrlSyntax($wsdl, 's+')){
            if ($canviewerrors) {
                $this->content->text = get_string('course_signals_not_working', 'block_course_signals');
            }
	        return $this->content;
        }

        //For adding certificate options
        $parameters = array();
        $certificate = get_config('blocks/course_signals', 'content_certificate_file');
        $certpassword = get_config('blocks/course_signals', 'certificate_password');
        if (!empty($certificate)) {
            $parameters['local_cert'] = $certificate;
            if (!empty($certpassword)) {
                $parameters['passphrase'] = $certpassword;
            }
        }

        //user sourcedid
        $user_sourcedid_config = block_course_signals_utils::get_config_field_userid();
        $sourseid = $USER->$user_sourcedid_config;
        //section sourcedid
        $section_sourcedid_config = block_course_signals_utils::get_config_field_userid();
        $sectionsourcedid = $COURSE->$section_sourcedid_config;
        if (empty($sourseid) || empty($sectionsourcedid)){//must have sourceid
            if ($canviewerrors) {
                $this->content->text = get_string('config_user_sourceid_empty', 'block_course_signals');
            }
            return $this->content;
        }

        $username = get_config('blocks/course_signals', 'webservice_username');
        $pass = get_config('blocks/course_signals', 'webservice_password');

        //result
        $client = new course_signal_service_client($wsdl, $username, $pass, $parameters);
        $result = $client->get_signal_for_studentsourcedid($sourseid, $sectionsourcedid);

        if (empty($result)) {
            if ($client->internalerror()) {
                add_to_log($COURSE->id, 'course_signals', 'get signal error', null, $client->errormsg());
                if ($canviewerrors) {
                    $this->content->text  = get_string('webservice_error', 'block_course_signals');
                    $this->content->text .= $client->errormsg();
                }
            }
            return $this->content;
        }

        $context = get_system_context();

        $signal_params = array('RED' => 1, 'YELLOW' => 2, 'GREEN' => 3);
        $variable = strtolower($result->stoplight);
        $imgvalue = $signal_params[$result->stoplight];
        $text = get_config('blocks/course_signals', "signal_config_{$variable}_text");
        $image = get_config('blocks/course_signals', "signal_config_{$variable}_upload");
        if ($image == "{$variable}.png"){
            $src = $CFG->wwwroot.'/blocks/course_signals/signal_images/'.$image;
        }else{
            $src = $CFG->wwwroot.'/pluginfile.php/'.$context->id."/block_course_signals/icon/{$imgvalue}/".$image;
        }
        $this->content->text .= html_writer::tag('p', 'Signal');
        $this->content->text .= html_writer::empty_tag('img', array('src' => $src,
                                                                            'alt' => $result->stoplight, //TODO: add propper text
                                                                            'width' => '40',
                                                                            'height' => '40',
                                                                            'style' => 'display: block'));
        html_writer::empty_tag('< /br>');


        $signal_message = 'Course ID: '.$result->courseId.html_writer::empty_tag('br').
        				  'DourseTitle: '.$result->courseTitle.html_writer::empty_tag('br').
        				  'EmailPage: '.$result->emailPage.html_writer::empty_tag('br').
        				  'Grade: '.$result->grade.html_writer::empty_tag('br').
        				  'Stoplight: '.$result->stoplight.html_writer::empty_tag('br').
        				  'StudentLMSId: '.$result->studentLMSId.html_writer::empty_tag('br');

        $PAGE->requires->js_init_call('YAHOO.example.container.signal_message.setBody("'.$signal_message.'")');

        $this->content->text .= html_writer::tag('a', $text, array('href' => '#', 'title' => $text, 'id' => 'show_signal'));

        $this->content->text .= html_writer::tag('div', '', array('id' => 'container'));

        return $this->content;
    }

    public function instance_allow_config() {
        return true;
    }

    public function instance_allow_multiple(){
        return false;
    }

    public function instance_config_save($data){

        //get data
        $instanceid = $this->instance->id;
        $section_type = $data->{block_course_signals_edit_form::INPUT_SECTION_TYPE};

        $parentcourse = null;
        if(isset($data->{block_course_signals_edit_form::PARENTCOURSE})){
            $parentcourse = $data->{block_course_signals_edit_form::PARENTCOURSE};
        }

        $gradesparent = $data->{block_course_signals_edit_form::OPTIONSET_GRADESPARENT};
        $gradesme = $data->{block_course_signals_edit_form::OPTIONSET_GRADESME};
        $statparent = $data->{block_course_signals_edit_form::OPTIONSET_STATPARENT};
        $statme = $data->{block_course_signals_edit_form::OPTIONSET_STATME};

        $organizer = null;
        if(isset($data->{block_course_signals_edit_form::ORGANIZER_PAGES})){
            $organizer_page = $data->{block_course_signals_edit_form::ORGANIZER_PAGES};
            $organizer = implode(',', $organizer_page);
        }

        //save data in table course_signals_config
        block_course_signals_config::save($instanceid,
                                          $section_type,
                                          $parentcourse,
                                          $gradesparent,
                                          $gradesme,
                                          $statparent,
                                          $statme,
                                          $organizer);

        parent::instance_config_save($data);
    }

    function instance_delete() {
        global $DB;
        $dbman = $DB->get_manager();
        if ($dbman->table_exists('block_course_signals_config')) {
            $DB->delete_records('block_course_signals_config',array('instanceid' => $this->instance->id));
        }
        return true;
    }

}
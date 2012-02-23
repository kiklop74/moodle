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
 * Test the different web service protocols.
 *
 * @author dmiletic@moodlerooms.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package    course_signals
 * @subpackage webservice
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

class block_course_signals_generic_form extends moodleform {

    /**
     * Offers posibility to add elements to the form
     * @param MoodleQuickForm $mform
     */
    protected function custom_definition(MoodleQuickForm $mform) {}

    /**
     * generate web service parameters
     * @param object $data
     * @return array
     */
    protected function format_params($data) {
        return array();
    }

    public function definition() {
        global $CFG;

        $mform = $this->_form;


        $mform->addElement('header', 'wstestclienthdr', get_string('testclient', 'webservice'));

        //note: these values are intentionally PARAM_RAW - we want users to test any rubbish as parameters
        $data = $this->_customdata;
        if ($data['authmethod'] == 'simple') {
            $mform->addElement('text', 'wsusername', 'wsusername');
            $mform->addElement('text', 'wspassword', 'wspassword');
        } else  if ($data['authmethod'] == 'token') {
            $mform->addElement('text', 'token', 'token', array('size' => '32'));
        }

        $mform->addElement('hidden', 'authmethod', $data['authmethod']);
        $mform->setType('authmethod', PARAM_SAFEDIR);

        $this->custom_definition($mform);

        $mform->addElement('hidden', 'function');
        $mform->setType('function', PARAM_SAFEDIR);

        $mform->addElement('hidden', 'protocol');
        $mform->setType('protocol', PARAM_SAFEDIR);

        $this->add_action_buttons(true, get_string('execute', 'webservice'));
    }

    public function get_params() {
        if (!$data = $this->get_data()) {
            return null;
        }

        //set customfields
        if (!empty($data->customfieldtype)) {
            $data->customfields = array(array('type' => $data->customfieldtype, 'value' => $data->customfieldvalue));
        }

        // remove unused from form data
        unset($data->submitbutton);
        unset($data->protocol);
        unset($data->function);
        unset($data->wsusername);
        unset($data->wspassword);
        unset($data->token);
        unset($data->authmethod);
        unset($data->customfieldtype);
        unset($data->customfieldvalue);

        $params = $this->format_params($data);

        return $params;
    }
}


class block_course_signals_get_sections_form extends block_course_signals_generic_form {
    protected function custom_definition(MoodleQuickForm $mform) {
        $mform->addElement('text', 'faculty', 'faculty');
        $mform->addElement('text', 'term', 'term');
        $mform->addElement('text', 'startdate', 'startdate');
    }

    protected function format_params($data) {

        $params = array();
        $params['options'] = array();
        $pp = array('faculty', 'term', 'startdate');
        foreach ($pp as $value) {
            if (isset($data->$value)) {
                $params['options'][$value] = $data->$value;
            }
        }
        return $params;
    }
}

class block_course_signals_get_students_form extends block_course_signals_generic_form {
    protected function custom_definition(MoodleQuickForm $mform) {
        $mform->addElement('text', 'section', 'section');
    }

    protected function format_params($data) {
        $params = array();
        if (isset($data->section)) {
            $params['section'] = $data->section;
        }
        return $params;
    }
}

class block_course_signals_get_effort_form extends block_course_signals_generic_form {
    protected function custom_definition(MoodleQuickForm $mform) {

        $mform->addElement('text', 'sourcedid', 'sourcedid');
        $mform->addElement('text', 'courselmsid', 'courselmsid');
        $mform->addElement('text', 'userlmsid[0]', 'userlmsid[0]');
        $mform->addElement('text', 'userlmsid[1]', 'userlmsid[1]');
        $mform->addElement('text', 'userlmsid[2]', 'userlmsid[2]');
        $mform->addElement('text', 'startdate', 'startdate');
        $mform->addElement('text', 'enddate'  , 'enddate'  );
    }

    protected function format_params($data) {
        $params = array();
        $params['options'] = array();
        $pp = array('sourcedid','courselmsid', 'startdate', 'enddate');
        foreach ($pp as $value) {
            if (isset($data->$value) && !empty($data->$value)) {
                $params['options'][$value] = $data->$value;
            }
        }

        if (isset($data->userlmsid) && !empty($data->userlmsid)) {
            $params['options']['userlmsid'] = array();
            foreach ($data->userlmsid as $value) {
                if (!empty($value)) {
                    $params['options']['userlmsid'][] = $value;
                }
            }
        }

        return $params;
    }
}

class block_course_signals_are_assesments_available_form extends block_course_signals_generic_form {
    protected function custom_definition(MoodleQuickForm $mform) {
        $mform->addElement('text', 'sourcedid', 'sourcedid');
        $mform->addElement('text', 'courselmsid', 'courselmsid');
    }

    protected function format_params($data) {
        $params = array();
        $params['options'] = array();
        if (isset($data->sourcedid)) {
            $params['options']['sourcedid'] = $data->sourcedid;
        }
        if (isset($data->courselmsid)) {
            $params['options']['courselmsid'] = $data->courselmsid;
        }

        return $params;
    }
}

class block_course_signals_get_assesments_form extends block_course_signals_generic_form {
    protected function custom_definition(MoodleQuickForm $mform) {
        $mform->addElement('text', 'section', 'section');
    }

    protected function format_params($data) {
        $params = array();
        if (isset($data->section)) {
            $params['section'] = $data->section;
        }
        return $params;
    }
}

class block_course_signals_get_assesment_results_form extends block_course_signals_generic_form {
    protected function custom_definition(MoodleQuickForm $mform) {
        $mform->addElement('text', 'sourcedid', 'sourcedid');
        $mform->addElement('text', 'courselmsid', 'courselmsid');
        $mform->addElement('text', 'assessmentsids[0]', 'assessmentsids[0]');
        $mform->addElement('text', 'assessmentsids[1]', 'assessmentsids[1]');
        $mform->addElement('text', 'assessmentsids[2]', 'assessmentsids[2]');
    }

    protected function format_params($data) {
        $params = array();
        $params['options'] = array();
        if (isset($data->sourcedid)) {
            $params['options']['sourcedid'] = $data->sourcedid;
        }
        if (isset($data->courselmsid)) {
            $params['options']['courselmsid'] = $data->courselmsid;
        }
        if (isset($data->assessmentsids)) {
            $params['options']['assessmentsids'] = array();
            foreach ($data->assessmentsids as $value) {
                if (!empty($value)) {
                    $params['options']['assessmentsids'][] = $value;
                }
            }
        }
        return $params;
    }
}

class block_course_signals_get_faculty_form extends block_course_signals_generic_form {

    protected function custom_definition(MoodleQuickForm $mform) {
        $mform->addElement('text', 'id', 'id');
        $mform->addElement('text', 'name', 'name');
        $mform->addElement('text', 'email', 'email');
    }

    protected function format_params($data) {
        $params = array();
        $pp = array('id', 'name', 'email');
        foreach ($pp as $value) {
            if (isset($data->$value)) {
                $params['options'][$value] = $data->$value;
            }
        }
        return $params;
    }
}

class block_course_signals_get_student_form extends block_course_signals_generic_form {

    protected function custom_definition(MoodleQuickForm $mform) {
        $mform->addElement('text', 'id', 'id');
        $mform->addElement('text', 'name', 'name');
        $mform->addElement('text', 'email', 'email');
    }

    protected function format_params($data) {
        $params = array();
        $pp = array('id', 'name', 'email');
        foreach ($pp as $value) {
            if (isset($data->$value)) {
                $params['options'][$value] = $data->$value;
            }
        }
        return $params;
    }
}

class block_course_signals_get_facultyroles_form extends block_course_signals_generic_form {}

class block_course_signals_get_studentroles_form extends block_course_signals_generic_form {}

class block_course_signals_get_lmsusers_form extends block_course_signals_generic_form {
    protected function custom_definition(MoodleQuickForm $mform) {
        $mform->addElement('text', 'sourcedids[0]', 'sourcedids[0]');
        $mform->addElement('text', 'sourcedids[1]', 'sourcedids[1]');
        $mform->addElement('text', 'sourcedids[2]', 'sourcedids[2]');
    }

    protected function format_params($data) {
        $params['sourcedids'] = array();
        if (isset($data->sourcedids)) {
            foreach ($data->sourcedids as $value) {
                if (!empty($value)) {
                    $params['sourcedids'][] = $value;
                }
            }
        }

        return $params;
    }

}

class block_course_signals_get_extusers_form extends block_course_signals_generic_form {
    protected function custom_definition(MoodleQuickForm $mform) {
        $mform->addElement('text', 'usersidlms[0]', 'usersidlms[0]');
        $mform->addElement('text', 'usersidlms[1]', 'usersidlms[1]');
        $mform->addElement('text', 'usersidlms[2]', 'usersidlms[2]');
    }

    protected function format_params($data) {
        $params['usersidlms'] = array();
        if (isset($data->usersidlms)) {
            foreach ($data->usersidlms as $value) {
                if (!empty($value)) {
                    $params['usersidlms'][] = $value;
                }
            }
        }

        return $params;
    }

}
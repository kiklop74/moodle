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
 * @author    Germán Vitale <gvitale@moodlerooms.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/form/filepicker.php');
require_once($CFG->libdir.'/form/filemanager.php');

class course_signals_multi_checkbox_roles extends admin_setting_pickroles {

    public function write_setting($data) {
        $count = count($data);
        if ($count == 1) {
            return get_string('error_choose_one_role', 'block_course_signals');
        }
        return parent::write_setting($data);
    }

    public function load_choices() {
        global $CFG, $DB;
        if (during_initial_install()) {
            return false;
        }
        if (is_array($this->choices)) {
            return true;
        }
        if ($roles = $DB->get_records_select('role', 'id <> '.$CFG->guestroleid.' and shortname <> "user"', null, null, 'id, name, shortname')) {
            $this->choices = array();

            foreach($roles as $role) {
                if ($role->shortname == 'teacher' || $role->shortname == 'editingteacher') {
                    $this->defaultsetting[] = $role->id;
                }
                $this->choices[$role->id] = format_string($role->name);
            }
            return true;
        } else {
            return false;
        }
    }
}

/**
* setting to uploud file with formlibs
* */

class course_signals_admin_setting_upload extends admin_setting {

    public $filename;

    /**
    * Constructor
    * @param string $name unique ascii name.
    * @param string $visiblename localised
    * @param string $description long localised info
    * @param string $defaultsetting name of the image to default
    * @param int    $maxbytes max number of bytes allowed
    * @param int    $maxfiles max number of files allowed
    * @param array  $accepted_types array with accepted types array('PNG', 'PNG')
    * @param int    $itemid
    *
    */

    public function __construct($name, $visiblename, $description, $defaultsetting, $maxbytes, $maxfiles, $accepted_types, $itemid) {
        global $CFG;

        $this->name = $name;
        $this->visiblename = $visiblename;
        $this->description = $description;
        $this->defaultsetting = $defaultsetting;
        $this->maxbytes = $maxbytes;
        $this->maxfiles = $maxfiles;
        $this->accepted_types = $accepted_types;
        $this->itemid = $itemid;
        $this->filename = '';

        parent::__construct($name, $visiblename, $description, $defaultsetting);
    }

    public function get_setting(){
        return true;
    }

    /**
    * Store new setting
    *
    * @param mixed $data string or array, must not be NULL
    * @return string empty string if ok, string error message otherwise
    */

    public function write_setting($data){
        global $CFG, $DB, $USER;

        $context = get_system_context();
        $validated = $this->validate($data);
        if ($validated !== true) {
            return $validated;
        }

        $file_contid = $DB->get_record_select('files', 'itemid = "'.$data.'" LIMIT 1', null, 'id, contextid');
        if($file_contid){
            $fs = get_file_storage();
            $files = $fs->get_area_files($file_contid->contextid, 'user', 'draft', $data, 'sortorder');
            foreach($files as $file){
                $file_record = array('contextid'=>$context->id, 'component'=>'block_course_signals', 'filearea'=>'icon', 'itemid'=>$this->itemid,
                                                             'filepath'=>'/', 'filename'=>$file->get_filename(), 'userid'=>$USER->id);
                $fs->create_file_from_storedfile($file_record, $file);
                return ($this->config_write($this->name, $file->get_filename()) ? '' : get_string('errorsetting', 'admin'));
            }
        }


        return '';
    }

    /**
    * Validate data before storage
    * @param string data
    * @return mixed true if ok string if error found
    */
    public function validate($data) {
        return true;
    }

    public function output_html($data){
        global $CFG, $USER, $COURSE, $PAGE, $OUTPUT, $DB;
        require_once("$CFG->dirroot/repository/lib.php");

        $context = get_system_context();

        $filepicker = new MoodleQuickForm_filepicker($this->name, 'filepicker', array('id' => 'id_'.$this->name,'name' => $this->name), array('subdirs' => 0, 'maxbytes' => $this->maxbytes, 'maxfiles' => $this->maxfiles, 'accepted_types' => $this->accepted_types ));

        $html = '';
        $html .= $filepicker->toHtml();

        //Current file
        $signal_config_current = get_config('blocks/course_signals');
        $filename = $signal_config_current->{$this->name};

        $src = '';
        $attributes = array('id' => $this->get_id().'_delete');
        if ($filename == $this->defaultsetting){
            $src = $CFG->wwwroot.'/blocks/course_signals/signal_images/'.$this->defaultsetting;
        }else{
            $fs = get_file_storage();
            $files = $fs->get_area_files($context->id, 'block_course_signals', 'icon', $this->itemid);
            foreach($files as $file){
                $src = $CFG->wwwroot.'/pluginfile.php/'.$context->id.'/block_course_signals/icon/'.$this->itemid.'/'.$filename;
            }
        }

        $draftitemid = $filepicker->getValue();

        $html .= html_writer::empty_tag('img', array('src' => $src, 'width' => '40', 'height' => '40'));
        $html .= html_writer::empty_tag('input', array('id' => $this->get_id(), 'name' => $this->get_full_name(), 'value' => $draftitemid, 'type' => 'hidden'));

        return format_admin_setting($this, $this->visiblename,$html,$this->description, true, '');
    }
}


class course_signals_admin_setting_configcheckbox extends admin_setting_configcheckbox{

    public $default_image;
    public $related_item;

    /**
    * Constructor
    * @param string $name unique ascii name.
    * @param string $visiblename localised
    * @param string $description long localised info
    * @param string $defaultsetting
    * @param string $default_image name of the image to default
    * @param string $related_item unique ascii Item Name to relate
    * @param int    $related_itemid itemid of the related element
	* @param string $yes value used when checked
    * @param string $no value used when not checked
    *
    */

    public function __construct($name, $visiblename, $description, $defaultsetting, $default_image, $related_item, $related_itemid, $yes='1', $no='0'){
        $this->default_image = $default_image;
        $this->related_item = $related_item;
        $this->related_itemid = $related_itemid;
        parent::__construct($name, $visiblename, $description, $defaultsetting, $yes='1', $no='0');
    }

    public function get_setting(){
        return false;
    }

    /**
    * Retrieves the current setting
    */
    public function get_setting() {
        return false;
    }

    /**
    * Saves the setting(s) provided in $data
    *
    * @param array $data An array of data, if not array returns empty str
    * @return mixed empty string on useless data or bool true=success, false=failed
    */
    public function write_setting($data) {
        if ((string)$data === $this->yes) { // convert to strings before comparison
            $context = get_system_context();
            $fs = get_file_storage();
            $files = $fs->get_area_files($context->id, 'block_course_signals', 'icon', $this->related_itemid, 'sortorder');
            foreach($files as $file){
                $file->delete();
            }
            return ($this->config_write($this->related_item, $this->default_image) ? '' : get_string('errorsetting', 'admin'));
            $data = $this->yes;
        } else {
            $data = $this->no;
        }
        return '';
    }

    /**
     * Returns an XHTML checkbox field
     *
     * @param string $data If $data matches yes then checkbox is checked
     * @param string $query
     * @return string XHTML field
     */
    public function output_html($data, $query='') {
            $default = $this->get_defaultsetting();

        if (!is_null($default)) {
            if ((string)$default === $this->yes) {
                $defaultinfo = get_string('checkboxyes', 'admin');
            } else {
                $defaultinfo = get_string('checkboxno', 'admin');
            }
        } else {
            $defaultinfo = NULL;
        }

        if ((string)$data === $this->yes) { // convert to strings before comparison
            $checked = 'checked="checked"';
        } else {
            $checked = '';
        }

        $disabled = '';
        if (get_config('blocks/course_signals', $this->related_item) ==  $this->default_image){
            $disabled = 'disabled="disabled"';//checkbox disabled
        }

        return format_admin_setting($this, $this->visiblename,
        '<div class="form-checkbox defaultsnext" ><input type="hidden" name="'.$this->get_full_name().'" value="'.s($this->no).'" /> '
            .'<input type="checkbox" id="'.$this->get_id().'" name="'.$this->get_full_name().'" value="'.s($this->yes).'" '.$checked.$disabled.' /></div>',
        $this->description, true, '', $defaultinfo, $query);
    }


}


















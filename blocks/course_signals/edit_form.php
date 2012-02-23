<?php
require_once($CFG->dirroot.'/blocks/course_signals/lib/section.php');

class block_course_signals_edit_form extends block_edit_form {

    const PREXIF_CONFIG = 'config_';
    const INPUT_SECTION_TYPE = 'course_section_types';

    const SECTION_TYPE_METACOURSE = 'metacourse';
    const SECTION_TYPE_COURSE = 'course';
    const SECTION_TYPE_GROUP = 'group';
    const SECTION_TYPE_CUSTOMPARENT = 'customparent';

    const PARENTCOURSE = 'parentcourse';
    const OPTIONSET_GRADESPARENT = 'gradesparent';
    const OPTIONSET_GRADESME = 'gradesme';
    const OPTIONSET_STATPARENT = 'statparent';
    const OPTIONSET_STATME = 'statme';

    const ORGANIZER_PAGES = 'organizer_pages';

    /**
     * @param MoodleQuickForm $mform
     */
    protected function specific_definition($mform){
        global $CFG, $COURSE, $DB;

        $mform->addElement('header', 'course_section_settings', get_string('course_section_settings', 'block_course_signals'));

        $mform->addElement('radio', self::PREXIF_CONFIG.self::INPUT_SECTION_TYPE, get_string('course', 'block_course_signals'), '',block_course_signals_section::TYPE_COURSE);
        $mform->addElement('static', 'coursedescription', '&nbsp;', get_string('section_maps_to_course', 'block_course_signals'));

        $courseparams = array( 'disabled' => 'disabled' );
        if ($DB->get_field_select('groups', 'id', "courseid='".$COURSE->id."' LIMIT 1")) {
            $courseparams = array();
        }
        $mform->addElement('radio', self::PREXIF_CONFIG.self::INPUT_SECTION_TYPE, get_string('course_group', 'block_course_signals'), '', block_course_signals_section::TYPE_GROUP, $courseparams);
        $mform->addElement('static', 'groupdescription', '&nbsp;', get_string('section_maps_to_course_group', 'block_course_signals'));

        //determine if course is linked to a metacourse
        $metaparams = array();
        $mform->addElement('radio', self::PREXIF_CONFIG.self::INPUT_SECTION_TYPE, get_string('metacourse', 'block_course_signals'), '', block_course_signals_section::TYPE_METACOURSE, $metaparams);
        $mform->addElement('static', 'metacoursedescription', '&nbsp;', get_string('section_maps_to_this_course_metacourse', 'block_course_signals'));

        $mform->addElement('radio', self::PREXIF_CONFIG.self::INPUT_SECTION_TYPE, get_string('custom_parent_course', 'block_course_signals'), '', block_course_signals_section::TYPE_CUSTOMPARENT);
        $mform->addElement('static', 'customparentdescription', '&nbsp;', get_string('section_maps_to_this_course_make_sure', 'block_course_signals'));

        $mform->setDefault(self::PREXIF_CONFIG.self::INPUT_SECTION_TYPE, 'course');

        $mform->addElement('header', 'grades_statistics', get_string('title_grades_statistics', 'block_course_signals'));

        $courses = $DB->get_records_select('course', 'format!="site"', null, null, 'id, shortname');
        $course_options = array();
        foreach($courses as $course_option){
            $course_options[$course_option->id] = $course_option->shortname;
        }
        $mform->addElement('select', self::PREXIF_CONFIG.self::PARENTCOURSE, get_string('parent_course', 'block_course_signals'), $course_options);

        $mform->addElement('advcheckbox', self::PREXIF_CONFIG.self::OPTIONSET_GRADESPARENT, get_string('parent_grades', 'block_course_signals'), get_string('grades_from_parent_course', 'block_course_signals'), null, array(0, 1));
        $mform->addElement('advcheckbox', self::PREXIF_CONFIG.self::OPTIONSET_GRADESME, get_string('course_grades', 'block_course_signals'), get_string('grades_from_this_course', 'block_course_signals'), null, array(0, 1));
        $mform->addElement('advcheckbox', self::PREXIF_CONFIG.self::OPTIONSET_STATPARENT, get_string('parent_stat', 'block_course_signals'), get_string('statistics_from_parent_course', 'block_course_signals'), null, array(0, 1));
        $mform->addElement('advcheckbox', self::PREXIF_CONFIG.self::OPTIONSET_STATME, get_string('course_stat', 'block_course_signals'), get_string('statistics_from_this_course', 'block_course_signals'), null, array(0, 1));


        $mform->addElement('header', self::ORGANIZER_PAGES, get_string('organizer_pages', 'block_course_signals'));
        $course_modsinfo = get_fast_modinfo($COURSE);
        $activities = array();
        foreach ($course_modsinfo->get_cms() as $course_modinfo){
            $activities[$course_modinfo->id] = $course_modinfo->modname.': '.$course_modinfo->name;
        }
        $select = $mform->addElement('select', self::PREXIF_CONFIG.self::ORGANIZER_PAGES, get_string('activities_organize_pages', 'block_course_signals'), $activities);
        $select->setMultiple(true);

        //disable elements
        $mform->disabledif('grades_statistics', self::PREXIF_CONFIG.self::INPUT_SECTION_TYPE, 'eq', self::SECTION_TYPE_COURSE);
        $mform->disabledif('grades_statistics', self::PREXIF_CONFIG.self::INPUT_SECTION_TYPE, 'eq', self::SECTION_TYPE_GROUP);
        $mform->disabledif(self::PREXIF_CONFIG.self::PARENTCOURSE, self::PREXIF_CONFIG.self::INPUT_SECTION_TYPE, 'neq', self::SECTION_TYPE_CUSTOMPARENT);
        $mform->disabledif(self::PREXIF_CONFIG.self::OPTIONSET_GRADESPARENT, self::PREXIF_CONFIG.self::INPUT_SECTION_TYPE, 'eq', self::SECTION_TYPE_COURSE);
        $mform->disabledif(self::PREXIF_CONFIG.self::OPTIONSET_GRADESPARENT, self::PREXIF_CONFIG.self::INPUT_SECTION_TYPE, 'eq', self::SECTION_TYPE_GROUP);
        $mform->disabledif(self::PREXIF_CONFIG.self::OPTIONSET_STATPARENT, self::PREXIF_CONFIG.self::INPUT_SECTION_TYPE, 'eq', self::SECTION_TYPE_COURSE);
        $mform->disabledif(self::PREXIF_CONFIG.self::OPTIONSET_STATPARENT, self::PREXIF_CONFIG.self::INPUT_SECTION_TYPE, 'eq', self::SECTION_TYPE_GROUP);
        $mform->disabledif(self::PREXIF_CONFIG.self::OPTIONSET_GRADESME, self::PREXIF_CONFIG.self::INPUT_SECTION_TYPE, 'eq', self::SECTION_TYPE_COURSE);
        $mform->disabledif(self::PREXIF_CONFIG.self::OPTIONSET_GRADESME, self::PREXIF_CONFIG.self::INPUT_SECTION_TYPE, 'eq', self::SECTION_TYPE_GROUP);
        $mform->disabledif(self::PREXIF_CONFIG.self::OPTIONSET_STATME, self::PREXIF_CONFIG.self::INPUT_SECTION_TYPE, 'eq', self::SECTION_TYPE_COURSE);
        $mform->disabledif(self::PREXIF_CONFIG.self::OPTIONSET_STATME, self::PREXIF_CONFIG.self::INPUT_SECTION_TYPE, 'eq', self::SECTION_TYPE_GROUP);

    }
}


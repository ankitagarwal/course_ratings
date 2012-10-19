<?php

// This file is part of Moodle block course_ratings - http://moodle.org/
/**
 * Form for Adding/editing Rating criteria.
 *
 * @copyright 2012 Ankit Kumar Agarwal
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class course_rating_edit_form extends moodleform {
    /**
     * The form definition
     */
    function definition() {
        global $PAGE, $DB;
        $mform    =& $this->_form;
        // Fields for editing HTML block title and contents.
        $mform->addElement('header', 'configheader', get_string('general'));

        $mform->addElement('text', 'criteria', get_string('criteria', 'block_course_ratings'));
        $mform->setType('criteria', PARAM_TEXT);
        $mform->addRule('criteria', get_string('missingcriteria', 'block_course_ratings'), 'required', 'server');

        $this->_customdata['courseid'] = 0;
        $courseid = empty($this->_customdata['courseid']) ? $PAGE->course->id : $this->_customdata['courseid'];
        $mform->addElement('hidden', 'courseid');
        $mform->setConstant('courseid', $courseid );

        $mform->addElement('hidden', 'cid');
        if (!empty ($this->_customdata['cid'])) {
            $cid = $this->_customdata['cid'];
            $rec = $DB->get_record('block_course_rating_criteria', array('id' => $cid));
            $mform->setConstant('cid', $cid);
            $mform->setConstant('courseid', $rec->courseid);
            $mform->setDefault('criteria', $rec->criteria);
        }

        $options = empty($this->_customdata['hassystemcap']) ? array ('1' => 'course') : array ('1' => 'course', '0' => 'site');
        $mform->addElement('select', 'level',get_string('select_level', 'block_course_ratings'), $options);
        $this->add_action_buttons(false, get_string('savechanges'));
    }
}

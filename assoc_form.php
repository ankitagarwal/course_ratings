<?php

// This file is part of Moodle block course_ratings - http://moodle.org/
/**
 * Form for Adding/editing Rating criteria.
 *
 * @copyright 2012 Ankit Kumar Agarwal
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class course_rating_assoc_form extends moodleform {
    /**
     * The form definition
     */
    function definition() {
        global $PAGE, $DB, $USER;
        $mform    =& $this->_form;
        // Fields for editing HTML block title and contents.
        $mform->addElement('header', 'configheader', get_string('assoc', 'block_course_ratings'));

        $mform->addElement('text', 'criteria', get_string('criteria', 'block_course_ratings'));
        $mform->setType('criteria', PARAM_TEXT);
        $mform->addRule('criteria', get_string('missingcriteria', 'block_course_ratings'), 'required', 'server');

        $courseid = empty($this->_customdata['courseid']) ? $PAGE->course->id : $this->_customdata['courseid'];
        $courses = array();
        if (is_siteadmin($USER)) {
            $results = get_courses('all', '', 'c.id, fullname');
            foreach ($results as $id => $result) {
                $courses[$id] = $result->fullname;
            }
        } elseif ($this->_customdata['hassystemcap']) {
            $results = enrol_get_my_courses('fullname');
            foreach ($results as $id => $result) {
                $courses[$id] = $result->fullname;
            }
        } else {
            $courses = array($courseid, $DB->get_field('course', 'fullname', array('id' => $courseid)));
        }
        $mform->addElement('select', 'course', get_string('course'), $courses, array('0'));
        $mform->addRule('course', get_string('missingccourse', 'block_course_ratings'), 'required', 'server');

        $this->add_action_buttons(false, get_string('addassoc', 'block_course_ratings'));
    }
}

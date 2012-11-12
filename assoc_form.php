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

        $courseid = empty($this->_customdata['courseid']) ? $PAGE->course->id : $this->_customdata['courseid'];

        $mform->addElement('hidden', 'courseid');
        $mform->setConstant('courseid', $courseid );

        $results = course_ratings::get_crits($courseid, $this->_customdata['hassystemcap'], 'criteria');
        foreach ($results as $id => $result) {
            $crits[$id] = $result->criteria;
        }
        $mform->addElement('select', 'criteria', get_string('criteria', 'block_course_ratings'), (array)$crits);
        $mform->addRule('criteria', get_string('missingcriteria', 'block_course_ratings'), 'required', 'server');

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
        $mform->addElement('select', 'course', get_string('course'), $courses);
        $mform->addRule('course', get_string('missingccourse', 'block_course_ratings'), 'required', 'server');

        $this->add_action_buttons(false, get_string('addassoc', 'block_course_ratings'));
    }
}

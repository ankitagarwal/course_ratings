<?php
// This file is part of Moodle block course_ratings - http://moodle.org/
/**
 * Adding/editing Rating criteria.
 *
 * @copyright 2012 Ankit Kumar Agarwal
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once('../../lib/formslib.php');
require_once('../../lib/tablelib.php');
require_once('criteria_form.php');
require_once('assoc_form.php');
require_once('locallib.php');

$courseid = required_param('courseid', PARAM_INT);
$cid = optional_param('cid', 0, PARAM_INT);
$delete = optional_param('delete', 0, PARAM_INT);
$sesskey = optional_param('sesskey', '', PARAM_TEXT);

// Cap checks.
require_login();
$coursecontext = context_course::instance($courseid);
require_capability('block/course_ratings:managecriteria', $coursecontext);
$hassystemcap = false;
if (has_capability('block/course_ratings:managecriteria', context_system::instance())) {
    $hassystemcap = true;
}

// Page setup.
$PAGE->set_context($coursecontext);
$PAGE->set_url('/blocks/course_ratings/edit.php', array('courseid' => $courseid));
$PAGE->set_title(get_string('managecriteria', 'block_course_ratings'));
$PAGE->set_pagelayout('standard');
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('managecriteria', 'block_course_ratings'));

// course rating object
$critinstance = new course_ratings($cid);

// Delete Criteria.
if ($delete && confirm_sesskey($sesskey)) {
    if ($hassystemcap || has_capability('blok/course_ratings:managecriteria', context_course::instance($rec->courseid))) {
        $critinstance->delete_crit();
        $cid = 0;
    } else {
        print_error('cannotdelete');
    }
    echo html_writer::tag('div', get_string('updated', 'block_course_ratings'));
}


$mform = new course_rating_edit_form(null, array('hassystemcap' => $hassystemcap, 'cid' => $cid, 'courseid' => $courseid));
// Process data.
if ($data = $mform->get_data()) {
    $critobj = new stdClass();
    $critobj->criteria = $data->criteria;
    $critobj->userid = $USER->id;
    $critcourse = $data->courseid;
    $critid = empty($data->cid) ? '' : $data->cid;
    if ($data->level == 0 && $hassystemcap) {
        $critobj->courseid = 0;
    } else if ($data->level == 1 && has_capability('block/course_ratings:managecriteria', context_course::instance($critcourse))) {
        $critobj->courseid = $critcourse;
    } else {
        print_error('invalidcourse');
    }
    if (!empty($critid)) {
        $critobj->id = $critid;
        $critinstance->update_crit($critobj);
    } else {
        $critinstance->update_crit($critobj);
    }
    echo html_writer::tag('div', get_string('updated', 'block_course_ratings'));
}

// Assoc form.
$aform = new course_rating_assoc_form(null, array('hassystemcap' => $hassystemcap, 'cid' => $cid, 'courseid' => $courseid));

// Process Assoc data.
if ($data = $aform->get_data()) {;

    // Perm check!
    require_capability('block/course_ratings:managecriteria', context_course::instance($data->course));

    if ($update = $critinstance->add_assoc($data->criteria, $data->course)) {
        echo html_writer::tag('div', get_string('updated', 'block_course_ratings'));
    } else {
        echo html_writer::tag('div', get_string('somethingwrong', 'block_course_ratings'));
    }
}

// Display forms.
$mform->display();
$aform->display();

// Display existing criteria.
$critinstance->display_crits($courseid, $hassystemcap);

echo $OUTPUT->footer();
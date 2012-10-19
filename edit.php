<?php 
// This file is part of Moodle block course_ratings - http://moodle.org/
/**
 * Adding/editing Rating criteria.
 *
 * @copyright 2012 Ankit Kumar Agarwal
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

error_reporting(-1);
require_once('../../config.php');
require_once('../../lib/formslib.php');
require_once('criteria_form.php');
$courseid = required_param('courseid', PARAM_INT);
$cid = optional_param('cid', 0, PARAM_INT);

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

$mform = new course_rating_edit_form(null, array('hassystemcap' => $hassystemcap, 'cid' => $cid, 'courseid' => $courseid));
// Process data
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
        $DB->update_record('block_course_rating_criteria', $critobj);
    } else {
        $DB->insert_record('block_course_rating_criteria', $critobj);
    }
    echo html_writer::tag('div', get_string('updated', 'block_course_ratings'));
}
$mform->display();
echo $OUTPUT->footer();
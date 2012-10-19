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
require_once('criteria_form.php');
$courseid = required_param('courseid', PARAM_INT);
$cid = optional_param('cid', 0, PARAM_INT);
$delete = optional_param('delete', 0, PARAM_INT);

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

// Delete Criteria.
if ($delete) {
    // TODO implement delete
    // TODO implement delete chain removing all ratings
}

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

// Display form.
$mform->display();

$sql = "SELECT cr.*, c.fullname, u.firstname, u.lastname from {block_course_ratings_criteria} cr
        LEFT JOIN {course} c
            ON cr.courseid = c.id
        LEFT JOIN {user} u
            ON cr.userid = u.id";
if ($hassystemcap) {
    $where = " WHERE courseid = 0 OR courseid = ?";
} else {
    $where = " WHERE courseid = 0 OR courseid = ?";
}
$cirts = $DB->get_records_sql($sql, array ($courseid));
// Display existing criteria.
if (empty ($crits)) {
    echo html_writer::tag('div', get_string('nocrit', 'block_course_ratings'));
    echo $OUTPUT->footer();
    die;
}
$columns = array();
$headers = array();

$columns[]= 'checkbox';
$headers[]= null;
$columns[]= 'criteria';
$headers[]= get_string('criteria', 'block_course_ratings');
$columns[]= 'course';
$headers[]= get_string('course');
$columns[]= 'createdby';
$headers[]= get_string('createdby', 'block_course_ratings');

$table = new flexible_table('crit-report');

$table->define_columns($columns);
$table->define_headers($headers);
$table->define_baseurl($PAGE->url);


$table->sortable(true);
$table->collapsible(true);
$table->no_sorting('checkbox');

$table->set_up;
print_object($SITE);
foreach ($crits as $cid => $crit) {
    $row = array();
    $row[] = html_writer::tag('input', array('type' => 'hidden', 'name' => 'action', 'value' => 'delete'));
    $row[] = $crit->criteria;
    if ($crit->courseid != 0) {
        $courseurl = new moodle_url('/course/view.php', array('id' => $crit->courseid));
        $row[] = html_writer::link($courseurl->out(false), $crit->fullname);
    } else {
        $row[] = $SITE->name;
    }
    $userurl = new moodle_url('/course/view.php', array('id' => $crit->userid, 'course' => 'courseid'));
    if (!empty($crit->firstname)) {
        $row[] = html_writer::link($userurl->out(false), fullname($crit));
    } else {
        // User who created this has been deleted.
        $row[] = get_string('deleteduser', 'block_course_ratings');
    }
} 
echo $OUTPUT->footer();
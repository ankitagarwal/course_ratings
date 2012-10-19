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
        $DB->update_record('block_course_ratings_crit', $critobj);
    } else {
        $DB->insert_record('block_course_ratings_crit', $critobj);
    }
    echo html_writer::tag('div', get_string('updated', 'block_course_ratings'));
}

// Display form.
$mform->display();

$sql = "SELECT cr.*, c.fullname, u.firstname, u.lastname from {block_course_ratings_crit} cr
        LEFT JOIN {course} c
            ON cr.courseid = c.id
        LEFT JOIN {user} u
            ON cr.userid = u.id";
if ($hassystemcap) {
    // Return all since user has system context cap
    $where = "";
} else {
    $where = " WHERE cr.courseid = ?";
}
$sql = $sql.$where;
$crits = $DB->get_records_sql($sql, array ($courseid));
// Display existing criteria.
$columns = array();
$headers = array();

$columns[]= 'criteria';
$headers[]= get_string('criteria', 'block_course_ratings');
$columns[]= 'course';
$headers[]= get_string('course');
$columns[]= 'createdby';
$headers[]= get_string('createdby', 'block_course_ratings');
$columns[]= 'checkbox';
$headers[]= null;

$table = new flexible_table('crit-report');

$table->define_columns($columns);
$table->define_headers($headers);
$table->define_baseurl($PAGE->url);


$table->sortable(true);
$table->collapsible(true);
$table->no_sorting('checkbox');

$table->setup();
foreach ($crits as $cid => $crit) {
    $row = array();
    $row[] = $crit->criteria;
    if ($crit->courseid != 0) {
        $courseurl = new moodle_url('/course/view.php', array('id' => $crit->courseid));
        $row[] = html_writer::link($courseurl->out(false), $crit->fullname);
    } else {
        $row[] = $SITE->fullname;
    }
    $userurl = new moodle_url('/user/view.php', array('id' => $crit->userid, 'course' => $courseid));
    if (!empty($crit->firstname)) {
        $row[] = html_writer::link($userurl->out(false), fullname($crit));
    } else {
        // User who created this has been deleted.
        $row[] = get_string('deleteduser', 'block_course_ratings');
    }
    //TODO replace below thing with delete and edit links
    $row[] = html_writer::tag('input', array('type' => 'hidden', 'name' => 'action', 'value' => 'delete'));
    $table->add_data($row);
} 
$table->finish_output();
echo $OUTPUT->footer();
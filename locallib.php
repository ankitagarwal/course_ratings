<?php

// This file is part of Moodle block course_ratings - http://moodle.org/
/**
 * Form for Adding/editing Rating criteria.
 *
 * @copyright 2012 Ankit Kumar Agarwal
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class course_ratings {

    /* @Var $id int criteria id */
    var $id;

    /* @var $crit stdClass criteria object */
    var $crit = null;

    /** Constructor for the class.
     *
     * @param int $cid criteria id.
     */
    function __construct($cid = null) {
        global $DB;
        if (!empty($cid)) {
            //if a cid is passed it must represent a criteria
            $this->crit = $DB->get_record('block_course_ratings_crit', array('id' => $cid), '*', MUST_EXIST);
            $this->id = $cid;
        }
    }

    /** Set the criteria id.
     *
     *
     * @param int $cid criteria id.
     *
     * @return bool
     */
    function set_crit($cid = null) {
        global $DB;
        if (!empty($cid)) {
            // if a cid is passed it must represent a criteria
            $this->crit = $DB->get_record('block_course_ratings_crit', array('id' => $cid), '*', MUST_EXIST);
            $this->id = $cid;
            return true;
        }
        return false;
    }

    /** Set the criteria id.
     *
     * @param int $cid criteria id.
     *
     * @return stdClass crit object
     */
    function return_crit($cid = null) {
        global $DB;
        if (!empty($cid)) {
            return $DB->get_record('block_course_ratings_crit', array('id' => $cid), '*', MUST_EXIST);
        }
        return $this->crit;
    }

    /** Return criteria based on given conditions
     *
     * @param int $courseid course id.
     * @param bool $returnall return all criterias?
     * @param
     *
     * @return Mixed array of criteria objects or false
     */
    static function get_crits($courseid = null, $returnall = false, $fields = '*') {
        global $DB;

        if ($fields === '*') {
            $fields = 'cr.*, c.fullname, u.firstname, u.lastname';
        } else {
            // Always get id to keep first column unique
            $fields = 'cr.id, '. $fields;
        }
        $sql = "SELECT $fields from {block_course_ratings_crit} cr
                LEFT JOIN {course} c
                    ON cr.courseid = c.id
                LEFT JOIN {user} u
                    ON cr.userid = u.id";
        if ($returnall) {
            // Return all since user has system context cap
            $where = "";
        } else {
            $where = " WHERE cr.courseid = ?";
        }
        $sql = $sql.$where;
        return $DB->get_records_sql($sql, array ($courseid));
    }

    /** Delete a crit
     *
     * @param int $cid criteria id.
     *
     * @return bool
     */
    function delete_crit($cid = null) {
        global $DB;

        if (empty($cid)) {
            if ($this->id != null) {
                $cid = $this->id;
            } else {
                return false;
            }
        }
        $DB->delete_records('block_course_ratings_crit', array('id' => $cid));
        $DB->delete_records('block_course_ratings_assoc', array('cid' => $cid));
        $DB->delete_records('block_course_ratings_rating', array('cid' => $cid));
        return true;
    }
    /** update or create a crit
     *
     * @param stdClass $critobj criteria object.
     *
     * @return mixed true/false/id
     */
    function update_crit($critobj) {
        global $DB;
        if(empty($critobj) || !is_object($critobj)) {
            return false;
        }
        // New record
        if (empty($critobj->id)) {
            return $DB->insert_record('block_course_ratings_crit', $critobj);
        } else {
        // Update record
            return $DB->update_record('block_course_ratings_crit', $critobj);
        }
    }

    /**  Insert rating
     *
     * @param int $courseid
     * @param int $cid
     * @param int $userid
     * @param int $rating
     *
     * @return mixed id on sucess, else false.
     */
    function insert_rating($courseid, $cid, $userid, $rating) {
        global $DB;

        if (empty($courseid) || empty($critid) || empty($userid) || empty($rating)) {
            return false;
        }
        if (!is_int($courseid) || !is_int($critid) || !is_int($userid) || !is_int($rating)) {
            return false;
        }
        if ($DB->record_exists('block_course_rating', array('userid' => $userid, 'courseid' => $courseid, 'cid' => $cid))) {
            return false;
        }
        $obj = new stdClass();
        $obj->userid = $userid;
        $obj->courseid = $courseid;
        $obj->cid = $cid;
        $obj->rating = $rating;
        $obj->lastupdated = time();
        return $DB->insert_record('block_course_rating', $obj, true);
    }

    /** Delete  a rating
     *
     * @param int $rid rating id
     */
    function delete_rating($rid) {
        global $DB;

        $DB->delete_records('block_course_ratings_rating', array('rid' => $rid));
    }

    /** Display criterias
     *
     */
    function display_crits($courseid, $hassystemcap) {
        global $PAGE, $DB, $SITE, $OUTPUT;

        $crits = $this->get_crits($courseid, $hassystemcap);

        $columns = array();
        $headers = array();

        $columns[]= 'criteria';
        $headers[]= get_string('criteria', 'block_course_ratings');
        $columns[]= 'course';
        $headers[]= get_string('course');
        $columns[]= 'createdby';
        $headers[]= get_string('createdby', 'block_course_ratings');
        $columns[]= 'edit';
        $headers[]= null;
        $columns[]= 'delete';
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
            $url = $PAGE->url;
            $url->param('cid', $crit->id);
            $url->param('sesskey', sesskey());
            $row[] = html_writer::link($url, '<img src ='.$OUTPUT->pix_url('t/edit').' />', array('title' => get_string('edit'), 'class' => 'iconsmall', 'alt' => get_string('edit')));
            $url->param('delete', 1);
            $row[] = html_writer::link($url, '<img src ='.$OUTPUT->pix_url('t/delete').' />', array('title' => get_string('delete'), 'class' => 'iconsmall', 'alt' => get_string('delete')));
            $table->add_data($row);
        }
        $table->finish_output();
    }
}

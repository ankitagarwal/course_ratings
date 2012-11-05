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
     * @PARAM int $cid criteria id.
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
     * @PARAM int $cid criteria id.
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
     * @PARAM int $cid criteria id.
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
     * @PARAM int $courseid course id.
     * @PARAM bool $returnall return all criterias?
     *
     * @return Mixed array of criteria objects or false
     */
    function get_crits($courseid = null, $returnall = false) {
        global $DB;

        $sql = "SELECT cr.*, c.fullname, u.firstname, u.lastname from {block_course_ratings_crit} cr
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
     * @PARAM int $cid criteria id.
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
     * @PARAM stdClass $critobj criteria object.
     *
     * @return mixed true/false/id
     */
    function update_crit($critobj) {
        global $DB;
        if(empty($critobj) || !is_array($critobj)) {
            return true;
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
}

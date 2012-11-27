<?php
class block_course_ratings extends block_base {

    /** course id
     *
     * @var int
     */
    private $courseid;

    /* course context on which the block is appearing */
    private $coursecontext;

    public function init() {
        $this->title = get_string('pluginname', 'block_course_ratings');
    }
    public function get_content() {
        global $CFG;
        if (!empty($this->context)) {
            $this->courseid = get_courseid_from_context($this->context);
            $this->coursecontext = context_course::instance($this->courseid);
        }
        // We are not intentionally checking for $this->content.
        $this->content = new stdClass();
        $this->content->text = '';
        $this->content->footer = '';
        if (empty($this->courseid)) {
            // This should never happen.
            return $this->content;
        }
        if (has_capability('block/course_ratings:ratecourse', $this->coursecontext)) {
            // Show cute stars.
            $this->content->text = get_string('ratethis', 'block_course_ratings');
            $this->content->text .= "";
        }
        if (has_capability('block/course_ratings:managecriteria', $this->coursecontext)) {
            // Show the settings link.
            $this->content->footer = html_writer::link($CFG->wwwroot."/blocks/course_ratings/edit.php?courseid=".$this->courseid, get_string('settings'));
        }
        return $this->content;
    }
    /** We donot want multiple instance of the block on the same page.
     *
     * @see block_base::instance_allow_multiple()
     */
    public function instance_allow_multiple() {
      return false;
    }

    /** We allow the block to be displayed only in front page and course pages
     *
     * @see block_base::applicable_formats()
     */
    public function applicable_formats() {
        return array(
           'site-index' => true,
          'course-view' => true,
        );
    }

    /** Cron execution
     *
     * @return boolean
     */
    public function cron() {
        // Nothing to do atm.
        return true;
    }

    /** Why you want to doc such a cute block?
     *
     * @see block_base::instance_can_be_collapsed()
     */
    public function instance_can_be_collapsed() {
        return false;
    }

    /** We will let you hide us, but keep reminding about it
     *
     * @see block_base::instance_can_be_hidden()
     */
    public function instance_can_be_hidden() {
        return false;
    }
}
<?php
class block_course_ratings extends block_base {
    function init() {
        $this->title = get_string('pluginname', 'block_course_ratings');
    }
    function get_content() {
        return "Test content";
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
}
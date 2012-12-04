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
            // Hack to be removed once MDL-36334 is fixed
             $this->fire_yui();
            $this->content->text .= '<input type="text" name="myRatings" id="myRatings" value="3.7">

<div class="demo" id="rating1"></div>

<div class="demo" id="rating2">
    <input type="radio" name="ratingValue" value="Horrible" title="Horrible" />
    <input type="radio" name="ratingValue" value="Very bad" title="Very bad" />
    <input type="radio" name="ratingValue" value="Bad" title="Bad" />
    <input type="radio" name="ratingValue" value="Acceptable" title="Acceptable" />
    <input type="radio" name="ratingValue" value="Good" title="Good" />
    <input type="radio" name="ratingValue" value="Very good" title="Very good" />
    <input type="radio" name="ratingValue" value="Perfect" title="Perfect" />
</div>

<div class="demo" id="rating3">
    <input type="radio" name="ratingValue" value="v1" title="Horrible" />
    <input type="radio" name="ratingValue" value="v2" title="Very bad" />
</div>';
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

    /** Load up the rating yui widget
     *
     * Not using this as cannot get it working
     */

    function fire_yui() {
        //$this->page->requires->__counstructor();
        $modulepath = '/blocks/course_ratings/';
        $this->page->requires->js(new moodle_url($modulepath.'yui/load.js'));
        $this->page->requires->js(new moodle_url($modulepath.'yui/rate.js'));

        //$this->page->requires->js_init_call('Y.Ratings({ srcNode: "#myWidget_basic" }).render');

        //$this->page->requires->js_init_code()

        //$this->page->requires->yui_module(array('loader', 'gallery-ratings', 'moodle-block_course_ratings-load'), 'Y.Ratings({ srcNode: "#myWidget_basic" }).render');
        $this->page->requires->yui_module(array('loader', 'base', 'event', 'gallery-aui-rating', 'gallery-ratings', 'moodle-block_course_ratings-load'), 'M.block_course_ratings_load.init');
        $this->page->requires->yui_module(array('gallery-ratings', 'event', 'moodle-block_course_ratings-rate'), 'M.block_course_ratings.init', array());
        //$this->page->requires->js_init_call('M.block_course_ratings.init');
        //$this->page->requires->js_module($module);
        //$this->page->requires->js(new moodle_url($modulepath.'yui/rate.js'));
        //$this->page->requires->js_init_call('M.block_course_ratings.init');

        /*//$this->page->requires->js_init_call('', array('x'), false, $module);
        $module = array(
            'name' => 'block_course_ratings',
            'fullpath' => $modulepath.'yui/rate.js',
            'requires' => array('gallery-aui-rating')
        );
        //$this->page->requires->js_module($module);
       //$this->page->requires->js_init_call('M.block_course_ratings.init', array('x'), false, $module);
        $this->page->requires->yui_module(array('gallery-aui-ratings', 'moodle-block_course_ratings-rate'), '', array(), '2011.10.20-23-28', false);
        $this->page->requires->js_init_call('M.block_course_ratings.init');
        //$this->page->requires->js_init_call('M.block_course_ratings.init', array(), true, $module);*/
    }
}
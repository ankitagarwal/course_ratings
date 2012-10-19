<?php
class block_course_ratings extends block_base {
    function init() {
        $this->title = get_string('pluginname', 'block_course_ratings');
    }
    function get_content() {
        return "Test content";
    }
}
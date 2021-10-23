<?php

class block_course_unenrollment extends block_base
{

    function init() {
        $this->title = get_string('pluginname', 'block_course_unenrollment');
    }

    public function get_content() {
        if ($this->content !== null)
            return $this->content;

        $this->content         =  new stdClass;
        $this->content->text   = 'The content of our SimpleHTML block!';

        // The other code.
        global $COURSE;

        $url = new moodle_url('/blocks/course_unenrollment/view.php');
        $this->content->footer = html_writer::link($url, get_string('addpage', 'block_course_unenrollment'));

        return $this->content;
    }
}

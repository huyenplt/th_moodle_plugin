<?php

require_once("{$CFG->libdir}/formslib.php");

class course_unenrollment_form extends moodleform {
    
    function definition() {
        global $DB;
        
        $mform = $this->_form;
        $mform->addElement('header','displayinfo', get_string('textfields', 'block_course_unenrollment'));
        
        $mform->addElement('date_selector', 'startdate', get_string('from'));
		$mform->addElement('date_selector', 'enddate', get_string('to'));

        $courseArr = array();
        $courses = $DB->get_records('course');
        
        foreach ($courses as $course) {
            // print_object($course->id);
            $courseArr[$course->id - 1] = $course->fullname;
        }

        $options = array(
			'multiple' => true,
		);
		//$mform->addElement('autocomplete', 'areaids', get_string('search', 'block_course_unenrollment'), $courseArr, $options);
		$this->course_arr = \course_unenrollment\lib::get_allcourseid_form($mform);


        $radioarray = array();
		$radioarray[] = $mform->createElement('radio', 'filter', '', 'Daily', 'day');
		$radioarray[] = $mform->createElement('radio', 'filter', '', 'Weekly', 'week');
		$radioarray[] = $mform->createElement('radio', 'filter', '', 'Monthly', 'month');
        $mform->addGroup($radioarray, 'radioar', get_string('radiolabel', 'block_course_unenrollment'), array(' '), FALSE);
		$mform->setDefault('filter', 'day');

        $mform->addElement('checkbox', 'ratingtime', get_string('checkboxcontent', 'block_course_unenrollment'));

        $mform->addElement('submit', 'submit', get_string('submit', 'block_course_unenrollment'));

		// $this->add_action_buttons(true, get_string('submmit', 'block_course_unenrollment'));

    }
}
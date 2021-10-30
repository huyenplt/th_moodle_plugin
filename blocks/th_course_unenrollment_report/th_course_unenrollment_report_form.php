<?php

require_once("{$CFG->libdir}/formslib.php");

class th_course_unenrollment_report_form extends moodleform {
    
    function definition() {
        global $DB;
        
        $mform = $this->_form;
        $mform->addElement('header','displayinfo', get_string('textfields', 'block_th_course_unenrollment_report'));
        
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
		//$mform->addElement('autocomplete', 'areaids', get_string('search', 'block_th_course_unenrollment_report'), $courseArr, $options);
		$this->course_arr = \th_course_unenrollment_report\lib::get_allcourseid_form($mform);


        $radioarray = array();
		$radioarray[] = $mform->createElement('radio', 'filter', '', 'Daily', 'day');
		$radioarray[] = $mform->createElement('radio', 'filter', '', 'Weekly', 'week');
		$radioarray[] = $mform->createElement('radio', 'filter', '', 'Monthly', 'month');
        $mform->addGroup($radioarray, 'radioar', get_string('radiolabel', 'block_th_course_unenrollment_report'), array(' '), FALSE);
		$mform->setDefault('filter', 'day');

        $mform->addElement('checkbox', 'wholecourse', get_string('checkboxcontent', 'block_th_course_unenrollment_report'));

        // $mform->addElement('submit', 'submit', get_string('submit', 'block_th_course_unenrollment_report'));

		$this->add_action_buttons(true, get_string('submmit', 'block_th_course_unenrollment_report'));

    }
}
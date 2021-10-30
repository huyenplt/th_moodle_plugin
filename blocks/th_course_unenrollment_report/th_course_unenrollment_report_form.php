<?php

require_once("{$CFG->libdir}/formslib.php");

class th_course_unenrollment_report_form extends moodleform {
    
    function definition() {
        global $DB;
        
        $mform = $this->_form;
        $mform->addElement('header','displayinfo', get_string('textfields', 'block_th_course_unenrollment_report'));
        
        $mform->addElement('date_selector', 'startdate', get_string('fromdate', 'block_th_course_unenrollment_report'));
		$mform->addElement('date_selector', 'enddate', get_string('todate', 'block_th_course_unenrollment_report'));

        $courseArr = array();
        $courses = $DB->get_records('course');
        
        foreach ($courses as $course) {
            // print_object($course->id);
            $courseArr[$course->id - 1] = $course->fullname;
        }

        // $options = array(
		// 	'multiple' => true,
		// );
		//$mform->addElement('autocomplete', 'areaids', get_string('search', 'block_th_course_unenrollment_report'), $courseArr, $options);
		$this->course_arr = \th_course_unenrollment_report\lib::get_allcourseid_form($mform);


        $radioarray = array();
		$radioarray[] = $mform->createElement('radio', 'filter', '', get_string('daily', 'block_th_course_unenrollment_report'), 'day');
		$radioarray[] = $mform->createElement('radio', 'filter', '', get_string('weekly', 'block_th_course_unenrollment_report'), 'week');
		$radioarray[] = $mform->createElement('radio', 'filter', '', get_string('monthly', 'block_th_course_unenrollment_report'), 'month');
        $mform->addGroup($radioarray, 'radioar', get_string('radiolabel', 'block_th_course_unenrollment_report'), array(' '), FALSE);
		$mform->setDefault('filter', 'day');

        $mform->addElement('checkbox', 'wholecourse', get_string('checkboxcontent', 'block_th_course_unenrollment_report'));

        // $mform->addElement('submit', 'submit', get_string('submit', 'block_th_course_unenrollment_report'));

		$this->add_action_buttons(true, get_string('submit', 'block_th_course_unenrollment_report'));
    }

    function validation($data, $files) {
        $config = get_config('block_th_course_unenrollment_report');
        $restrict_day = $config->restrict_date;
        $restrict_week = $config->restrict_week;
        $restrict_month = $config->restrict_month;

        if ($data['enddate'] < $data['startdate']) {
            return array('enddate' => get_string('date_err', 'block_th_course_unenrollment_report'));
        }

        if ($data['filter'] == 'day') {
            $day_count = ($data['enddate'] - $data['startdate']) / (24*60*60) + 1;

            if($day_count > $restrict_day)
                return array('enddate' => get_string('date_restrict_err', 'block_th_course_unenrollment_report'));
        }

        if ($data['filter'] == 'week') {
            $start_week_monday = strtotime("this week monday", $data['startdate']);
            $end_week_monday = strtotime("this week monday", $data['enddate']);
		    $week_count = ($end_week_monday - $start_week_monday) / (7 * 24 * 60 * 60) + 1;
    
            if($week_count > $restrict_week)
                return array('enddate' => get_string('week_restrict_err', 'block_th_course_unenrollment_report'));
        }

        if ($data['filter'] == 'month') {
            $start_month_first_day = strtotime("first day of this month", $data['startdate']);
            $end_month_first_day = strtotime("first day of this month", $data['enddate']);

            $month_count = 0;
            for ($i = $start_month_first_day; $i <= $end_month_first_day; $i = strtotime("first day of this month +1 month", $i)) {
                $month_date_from_to[$month_count] = date('d/m/Y', strtotime("first day of this month", $i)) . ' - ' .
                date('d/m/Y', strtotime("last day of this month", $i));
                $month_count++;
            }
    
            if($month_count > $restrict_month)
                return array('enddate' => get_string('month_restrict_err', 'block_th_course_unenrollment_report'));
        }

        return array();
    }
}
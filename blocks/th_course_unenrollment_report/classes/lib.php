<?php
namespace th_course_unenrollment_report;

class lib {
    public function get_fullname_course($courseid) {
		global $DB;
		$sql = "SELECT fullname FROM {course} WHERE id = $courseid";
		$fullname = $DB->get_field_sql($sql);
		return $fullname;
	}

	public function get_shortname_course($courseid) {
		global $DB;
		$sql = "SELECT shortname FROM {course} WHERE id = $courseid";
		$shortname = $DB->get_field_sql($sql);
		return $shortname;
	}

    public function get_courseid() {
		global $DB;
		$courses = $DB->get_records('course', array('visible' => 1), '', 'id,fullname,shortname,idnumber,category');

		$choice = array();
		foreach ($courses as $key => $value) {

			$n = $value->id;

			$choice[$key] = $n;
		}
		return $choice;
	}

    public static function get_allcourseid_form($mform) {
		global $DB;
		$courses = $DB->get_records('course', array('visible' => 1), '', 'id,fullname,shortname,idnumber,category');
		$choice = array();
		$choice[''] = '';
		$keyfrontcourse = 1;
		foreach ($courses as $key => $value) {
			if ($value->category == 0) {
				$keyfrontcourse = $key;
				continue;
			}

			$n = $value->fullname;

			// if (isset($value->shortname) && trim($value->shortname) !== '') {
			// 	$n .= ',' . $value->shortname;
			// }

			if (isset($value->idnumber) && trim($value->idnumber) !== '') {
				$n .= ',' . $value->idnumber;
			}

			$choice[$key] = $n;
		}
		unset($courses[$keyfrontcourse]);

		$options = array(
			'multiple' => true,
			'noselectionstring' => get_string('noselection', 'block_th_course_unenrollment_report'),
		);
		$mform->addElement('autocomplete', 'courseid', get_string('search', 'block_th_course_unenrollment_report'), $choice, $options);
		return $courses;
	}
}
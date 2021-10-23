<?php

use Sabberworm\CSS\Value\Value;

require_once('../../config.php');
require_once('course_unenrollment_form.php');
require_once $CFG->dirroot . '/local/thlib/lib.php';
require_once $CFG->dirroot . '/blocks/course_unenrollment/classes/lib.php';



global $DB, $OUTPUT, $PAGE, $COURSE;

$id = optional_param('id', 0, PARAM_INT);

if (!$course = $DB->get_record('course', array('id' => $COURSE->id))) {
    print_error('invalidcourse', 'block_course_unenrollment', $COURSE->id);
}
require_login($COURSE->id);

$title = get_string('pluginname', 'block_course_unenrollment');
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_heading($title);
$PAGE->set_title($title);
$PAGE->set_url('/blocks/course_unenrollment/view.php');
$PAGE->set_pagelayout('standard');

$editurl = new moodle_url('/blocks/course_unenrollment/view.php');
$settingsnode = $PAGE->settingsnav->add($title, $editurl);
$settingsnode->make_active();

echo $OUTPUT->header();

$mform = new course_unenrollment_form();
$mform->display();
$formdata = $mform->get_data();

print_object($formdata);

// if ($course_unenrollment_form->is_cancelled()) {
// 	// Cancelled forms redirect to the course main page.
// 	$courseurl = new moodle_url('/my');
// 	redirect($courseurl);
// }


if($formdata) {
    // $lib = new course_unenrollment\lib();
    $params_aaa = array('courseid' => 10);

    $start_date = $formdata->startdate;
    $end_date = $formdata->enddate;

    $unenroll_user_sql_aaa = "
                    SELECT ue.timeend, COUNT(ue.userid), c.fullname, c.shortname
                    FROM mdl_course as c, mdl_user_enrolments as ue, mdl_enrol as e
                    WHERE e.courseid = c.id AND ue.enrolid = e.id AND c.id = :courseid
                    GROUP BY ue.timeend;
                ";

    $temp_user_aaa = $DB->get_records_sql($unenroll_user_sql_aaa, $params_aaa);
    print_object($temp_user_aaa);

    foreach($temp_user_aaa as $keyaaa => $valueaaa) {
        echo $keyaaa . '-';
        if (date('d/m/Y', $keyaaa) == date('d/m/Y', $end_date))
            $count_user++;
    }

    $ab = $end_date - 8*24*60*60;
    print_object(userdate($end_date - 8*24*60*60));
    // print_object(userdate(strtotime("this day", $end_date)));
    print_object(userdate('1635833640'));
    $aaaa = strtotime(date("m/d/Y",'1635833640'));
    print_object($aaaa);
    print_object(userdate($aaaa));

    if ($ab == $aaaa) {
        echo 'absjagslajs';
    }
    


    // echo userdate(strtotime("this week monday", $start_date)) . '</br>';
    // echo userdate(strtotime("this week sunday", $start_date)) . '</br>';

    // echo userdate(strtotime("this week monday", $end_date)) . '</br>';
    // echo userdate(strtotime("this week sunday", $end_date)) . '</br>';

    $table = new html_table();

    $headrows = new html_table_row();

    $cell = new html_table_cell(get_string('course_short', 'block_course_unenrollment'));
    $cell->attributes['class'] = 'cell headingcell';
    $cell->header = true;
    $headrows->cells[] = $cell;

    $cell = new html_table_cell(get_string('course_full', 'block_course_unenrollment'));
    $cell->attributes['class'] = 'cell headingcell';
    $cell->header = true;
    $headrows->cells[] = $cell;

    $courseid_arr = $formdata->courseid;

    print_object($courseid_arr);

    if($formdata->filter == 'day') {
        $count_date = 0;
        $total_by_column = array();
        for($i = $start_date; $i <= $end_date; $i=$i+24*60*60) {
            $cell = new html_table_cell(date('d/m/Y', $i));
            $cell->attributes['class'] = 'cell headingcell';
            $cell->header = true;
            $headrows->cells[] = $cell;
            $total_by_column[$count_date] = 0;
            $count_date++;
        }

        foreach($courseid_arr as $key => $courseid) {
            $coursesql = "SELECT c.id, c.fullname, c.shortname
            FROM mdl_course as c
            WHERE c.id = :courseid";

            $params = array('courseid' => $courseid);
            $temp = $DB->get_records_sql($coursesql, $params);

            $value = $temp[$courseid];
            $row = new html_table_row();
            $cell = new html_table_cell($value->shortname);
            // $cell->attributes['data-search'] = $key;
            $row->cells[] = $cell;

            $cell = new html_table_cell($value->fullname);
            // $cell->attributes['data-search'] = $key;
            $row->cells[] = $cell;

            $total_by_row = 0;
            $count_date = 0;
            for($i = $start_date; $i <= $end_date; $i=$i+24*60*60) {
                $count_user = 0;
                $unenroll_user_sql = "
                    SELECT ue.*, c.fullname, c.shortname
                    FROM mdl_course as c, mdl_user_enrolments as ue, mdl_enrol as e
                    WHERE e.courseid = c.id AND ue.enrolid = e.id AND c.id = :courseid
                ";

                $temp_user = $DB->get_records_sql($unenroll_user_sql, $params);

                foreach($temp_user as $key1 => $value) {
                    if (date('d/m/Y', $i) == date('d/m/Y', $value->timeend))
                        $count_user++;
                }
                $total_by_column[$count_date] += $count_user;
                $count_date++;
                $total_by_row += $count_user;

                $cell = new html_table_cell($count_user);
                $row->cells[] = $cell;

            }
            // total by row
            $cell = new html_table_cell($total_by_row);
            $cell->header = true;
            $row->cells[] = $cell;
            $table->data[] = $row;
        }
        // total by column
        $row = new html_table_row();
        $cell = new html_table_cell('Total');
        $cell->header = true;
        $row->cells[] = $cell;

        $cell = new html_table_cell('');
        $cell->header = true;
        $row->cells[] = $cell;

        $total_total = 0;
        for ($i = 0; $i < count($total_by_column); $i++) {
            $cell = new html_table_cell($total_by_column[$i]);
            $cell->header = true;
            $row->cells[] = $cell;
            $total_total += $total_by_column[$i];
        }

        $cell = new html_table_cell($total_total);
        $cell->header = true;
        $row->cells[] = $cell;

        $table->data[] = $row;
    } 

    else if ($formdata->filter == 'week') {
        $start_week_monday = strtotime("this week monday", $start_date);
        $start_week_sunday = strtotime("this week sunday", $start_date);

        $end_week_monday = strtotime("this week monday", $end_date);
        $ennd_week_sunday = strtotime("this week sunday", $end_date);

        $week_count = ($end_week_monday - $start_week_monday)/(7*24*60*60) + 1;

        $week_date = array();
        for ($i = 0; $i < $week_count; $i++) {
            $week_date[$i] =  date('d/m/Y', $start_week_monday) . '</br>' . ' - '. date('d/m/Y', $start_week_sunday);
            $start_week_monday +=  7*24*60*60;
        }

        for ($i = 0; $i < $week_count; $i++) {
            $cell = new html_table_cell($week_date[$i]);
            $cell->attributes['class'] = 'cell headingcell';
            $cell->header = true;
            $headrows->cells[] = $cell;
            // $total_by_column[$count_date] = 0;
            // $count_date++;
        }

        foreach($courseid_arr as $key => $courseid) {
            $coursesql = "SELECT c.id, c.fullname, c.shortname
            FROM mdl_course as c
            WHERE c.id = :courseid";

            $params = array('courseid' => $courseid);
            $temp = $DB->get_records_sql($coursesql, $params);

            $value = $temp[$courseid];
            $row = new html_table_row();
            $cell = new html_table_cell($value->shortname);
            // $cell->attributes['data-search'] = $key;
            $row->cells[] = $cell;

            $cell = new html_table_cell($value->fullname);
            // $cell->attributes['data-search'] = $key;
            $row->cells[] = $cell;

            $table->data[] = $row;
        }

        print_object($week_date);

        echo "So tuan: " . $week_count;
    }

    $cell = new html_table_cell('Total');
    $cell->attributes['class'] = 'cell headingcell';
    $cell->header = true;
    $headrows->cells[] = $cell;
    // $headrows = array_shift($table->data);
    $table->head = $headrows->cells;
    $table->attributes = array('class' => 'table', 'border' => '1');
    $table->align[0] = 'center';
    $table->align[1] = 'center';

    echo html_writer::table($table);
}
echo $OUTPUT->footer();


?>
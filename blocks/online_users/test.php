<?php
// require_once($CFG->dirroot.'../../../config.php');
require_once '../../config.php';


// require_login();
$context = context_course::instance($COURSE->id);

// require_capability('moodle/course:viewreports', context_course::instance($COURSE->id));

$PAGE->set_url('/local/blocks/online_users/student.php');
$PAGE->set_context(context_course::instance($COURSE->id));
//$PAGE->set_pagelayout('admin');
$PAGE->set_heading('List Student');
$PAGE->set_pagelayout('incourse');



$courseid = required_param('id', PARAM_INT);

global $DB, $USER;


echo $OUTPUT->header();


echo $OUTPUT->heading("Danh sách học sinh của lớp ".$hd->fullname);


$table2 = new html_table();
$table2->head=array('STT', 'Tên Học Sinh');
$row = array('sdadasd', 'fullname');
$table2->data[]=$row;



echo html_writer::table($table2);

echo $OUTPUT->footer();


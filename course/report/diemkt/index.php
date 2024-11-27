<?php
// require_once($CFG->dirroot.'../../../config.php');
require_once '../../../config.php';
require_once($CFG->dirroot.'/course/report/lib.php');
require_once($CFG->dirroot.'/course/lib.php');

$courseid = required_param('id', PARAM_INT);
// require_login();
$context = context_course::instance($COURSE->id);

// require_capability('moodle/course:viewreports', context_course::instance($COURSE->id));
$PAGE->set_url(new moodle_url('/local/course/report/diemkt/index.php', array('id' => $courseid)));
$PAGE->set_context(context_course::instance($COURSE->id));
//$PAGE->set_pagelayout('admin');
$PAGE->set_heading(get_string('pluginname', 'coursereport_diemkt'));
$PAGE->set_pagelayout('report');




global $DB, $USER;


echo $OUTPUT->header();
$hdsql = 'SELECT c.fullname
        FROM  mdl_course c
        where c.id='.$courseid;
$heading = $DB->get_records_sql($hdsql);
foreach($heading as $heading){
    $hd = $heading->fullname;
}
echo $OUTPUT->heading("Bảng Điểm Môn ".$hd);


//$quiz_names = $DB->get_records_sql('SELECT DISTINCT name FROM {quiz} WHERE course = ? ORDER BY name', array($courseid));
$quiz_names = $DB->get_records_sql('SELECT DISTINCT name, timeopen FROM {quiz} WHERE course = ? ORDER BY timeopen', array($courseid));

$select_clauses = [];
foreach ($quiz_names as $quiz) {
    $select_clauses[] = "MAX(CASE WHEN q.name = '{$quiz->name}' THEN g.grade END) AS '{$quiz->name}'";
}
$select_clause = implode(", ", $select_clauses);

$sql = "SELECT 
            ROW_NUMBER() OVER (ORDER BY u.firstname) AS stt,
            u.firstname, 
            u.lastname, 
            $select_clause,
            AVG(g.grade) AS average_grade
        FROM 
            {user} u
        JOIN {quiz_grades} g ON u.id = g.userid
        JOIN {quiz} q ON g.quiz = q.id
        WHERE 
            q.course = ?
        GROUP BY 
            u.id, u.firstname, u.lastname
        ORDER BY 
            u.firstname";

// Execute the query
$grades = $DB->get_records_sql($sql, array($courseid));

// Display the results in a table
$table = new html_table();
$table->head = array('STT', 'Họ Tên Học Sinh');
foreach ($quiz_names as $quiz) {
    $table->head[] = $quiz->name;
}
$table->head[] = 'Điểm trung bình';

foreach ($grades as $grade) {
    $fullname = $grade->firstname . ' ' . $grade->lastname;
    $row = array($grade->stt, $fullname);
    foreach ($quiz_names as $quiz) {
        $diem = number_format($grade->{$quiz->name}, 2, '.', '');
        $row[] = $diem;
    }
    $diemtrungbinh = number_format($grade->average_grade, 2, '.', '');
    $row[] = $diemtrungbinh;
    $table->data[] = $row;
}

foreach ($table->data as $rowindex => $row) {
    foreach ($row as $colindex => $cell) {
        // Kiểm tra nếu cột không phải là cột thứ 2 (index 1)
        if ($colindex != 1) {
            // Thêm class vào các ô không phải cột thứ 2
            $table->data[$rowindex][$colindex] = html_writer::tag('span', $cell, array('class' => 'edm'));
        }
    }
}
foreach ($table->head as $index => $columnname) {
    //if ($index != 1) {
        $table->head[$index] = html_writer::tag('span', $columnname, array('class' => 'edm'));
    //}
}
echo html_writer::table($table);

echo $OUTPUT->footer();

echo "<style>
    h2{text-align: center; }
    td, th {padding: 10px}
    td, th {padding-right: 5em}
    table, td, th {border: 1px solid #dee2e6}
    tbody{border: 1px solid #ccc}
    table {border-collapse: separate; margin-bottom: 0em; height: 100%;}
    table {width: 490px}
    th {height: 40px}
    td{white-space: nowrap; vertical-align: middle}
    h5{color: purple}
    .edm{ text-align: center; display: block}
</style>";

?>

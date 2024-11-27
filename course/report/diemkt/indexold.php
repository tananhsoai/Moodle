<?php
// require_once($CFG->dirroot.'../../../config.php');
require_once '../../../config.php';
require_once($CFG->dirroot.'/course/report/lib.php');
require_once($CFG->dirroot.'/course/lib.php');

// require_login();
$context = context_course::instance($COURSE->id);

// require_capability('moodle/course:viewreports', context_course::instance($COURSE->id));
$cid=3;
$PAGE->set_url('/local/course/report/diemkt/index.php');
$PAGE->set_context(context_course::instance($COURSE->id));
//$PAGE->set_pagelayout('admin');
$PAGE->set_heading(get_string('pluginname', 'coursereport_diemkt'));
$PAGE->set_pagelayout('incourse');

$current_url = $PAGE->url;
parse_str(parse_url($current_url, PHP_URL_QUERY), $params);

$courseid = required_param('id', PARAM_INT);

global $DB, $USER;

$sql = "SELECT 

        ROW_NUMBER() OVER(ORDER BY u.firstname) AS stt, 

        u.firstname, u.lastname, q.name, g.grade, q.id

        FROM mdl_user u
        
        JOIN mdl_quiz_grades g ON u.id = g.userid

        JOIN mdl_quiz q on g.quiz = q.id

        WHERE q.course = ".$courseid."
        
        ORDER BY u.firstname;";

$result = $DB->get_records_sql($sql);
echo $OUTPUT->header();
$sql = 'SELECT c.fullname
        FROM  mdl_course c
        where c.id='.$courseid;
$heading = $DB->get_records_sql($sql);
foreach($heading as $heading){
    $hd = $heading->fullname;
}
echo $OUTPUT->heading("Bảng Điểm Môn ".$hd);

echo "<style>
    h2{text-align: center; }
    td, th {padding: 10px}
    td, th {padding-right: 6em}
    table, td, th {border: 1px solid #dee2e6}
    tbody{border: 1px solid #ccc}
    table {border-collapse: separate; margin-bottom: 2em; height: 100%;}
    table {width: 500px}
    th {height: 40px}
    td{white-space: nowrap; vertical-align: middle}
</style>";

echo '<div style="display: flex; justify-content: center;"><table> <tbody>';
echo "<tr>
<th>STT</th>
<th>Tên Học Sinh</th>
<th>Lần thi</th>
<th>Điểm</th>
</tr>";
$counter = 1;
foreach($result as $course){

    $diem = number_format($course->grade, 2, '.', '');
    $fullname = $course->firstname . ' ' . $course->lastname;
    echo '<tr>
    <td>'.$counter++.'</td>
    <td>'.$fullname.'</td>
    <td><a href="http://localhost/moodle/mod/quiz/view.php?id=6">'.$course->name.'<a></td>
    <td>'.$diem.'</td>
    </tr>';
                
}
echo "</tbody> </table></div>";

echo $OUTPUT->footer();


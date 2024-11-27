<?php
// require_once($CFG->dirroot.'../../../config.php');
require_once '../../config.php';


// require_login();
$context = context_course::instance($COURSE->id);

// require_capability('moodle/course:viewreports', context_course::instance($COURSE->id));

$PAGE->set_url('/local/blocks/online_users/classes.php');
$PAGE->set_context(context_course::instance($COURSE->id));
//$PAGE->set_pagelayout('admin');
$PAGE->set_heading('List Classes');
$PAGE->set_pagelayout('incourse');



$schoolid = required_param('id', PARAM_INT);

global $DB, $USER;

$sql = 'SELECT fullname
        FROM mdl_school 
        WHERE id='.$schoolid;

$hd = $DB->get_record_sql($sql);
echo $OUTPUT->header();


echo $OUTPUT->heading("Danh sách lớp của ".$hd->fullname);

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

//$result = $DB->get_records_sql('SELECT c.fullname, c.id FROM mdl_course c WHERE c.school_id='.$schoolid);

$sql = 'SELECT 
    c.fullname, 
    c.id, 
    u.firstname AS tengv, 
    u.lastname
FROM 
    mdl_course c
JOIN 
    mdl_context ct ON c.id = ct.instanceid
JOIN 
    mdl_role_assignments ra ON ra.contextid = ct.id
JOIN 
    mdl_user u ON ra.userid = u.id
WHERE 
    ra.roleid = 3
    AND c.school_id = '.$schoolid;

$result = $DB->get_records_sql($sql);





$table = new html_table();
$table->head=array('STT', 'Tên lớp', 'Giáo viên phụ trách', 'Sĩ số');
$counter = 1;
foreach($result as $class){

    $sql2 = 'SELECT u.firstname, u.lastname
        FROM mdl_user u
        JOIN mdl_role_assignments ra ON u.id = ra.userid
        JOIN mdl_context ct ON ct.id = ra.contextid
        JOIN mdl_course c ON c.id = ct.instanceid
        WHERE c.id = '.$class->id.' AND ra.roleid = 5';
    $rs = $DB->get_records_sql($sql2);
    $dem = 0;
    foreach($rs as $student){
        $dem++;                 
    }

    $tenlop='<a href="student.php?id='.$class->id.'">'.$class->fullname.'</a>';
    $teachername = $class->tengv.' '.$class->lastname;
    $row = array($counter++, $tenlop, $teachername, $dem);
    $table->data[]=$row;                
}
echo html_writer::table($table);

 echo'<h2>Danh sách giáo viên của '.$hd->fullname.'</h2>';

$sql = "SELECT u.firstname, u.lastname, u.id AS user_id, u.username, u.email
                        FROM mdl_user u
                        JOIN mdl_role_assignments ra ON u.id = ra.userid
                        JOIN mdl_context ctx ON ra.contextid = ctx.id
                        JOIN mdl_course c ON ctx.instanceid = c.id
                        WHERE c.id !=0 AND ra.roleid = (SELECT id FROM mdl_role WHERE shortname = 'editingteacher') AND c.school_id=".$schoolid."
                        GROUP BY u.id";
$rse = $DB->get_records_sql($sql);
// echo '<div class= "cuon" ><table> <tbody>';
// echo  "<tr>
//                 <th>STT</th>
//                 <th>User Name</th>    
//                 <th>Họ Tên</th>
//                 <th>Email</th>
//                 </tr>";
//             $counter = 1;
//             foreach($rse as $course){

//                 $giangvien = $course->firstname . ' ' . $course->lastname;
//                 echo '<tr>
//                 <td class="cc">'.$counter++.'</td>
//                 <td class="cc">'.$course->username.'</td>
//                 <td>'.$giangvien.'</a></td>
//                 <td class="cc">'.$course->email.'</td>
//                 </tr>';
                
//             }
//             $table .="</tbody> </table></div>";
//             $text = $table;
$tablegv = new html_table();
$tablegv->head=array('STT', 'Username', 'Họ Tên', 'Email');
$counter = 1;
foreach($rse as $teacher){
    $teachername = $teacher->firstname.' '.$teacher->lastname;
    $row = array($counter++, $teacher->username, $teachername, $teacher->email);
    $tablegv->data[]=$row;                
}
echo html_writer::table($tablegv);
echo $OUTPUT->footer();


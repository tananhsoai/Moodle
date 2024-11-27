<?php
// require_once($CFG->dirroot.'../../../config.php');
require_once '../../config.php';


// require_login();
$context = context_course::instance($COURSE->id);

// require_capability('moodle/course:viewreports', context_course::instance($COURSE->id));

$PAGE->set_url('/local/blocks/online_users/student.php');
$PAGE->set_context(context_course::instance($COURSE->id));
//$PAGE->set_pagelayout('admin');
$courseid = required_param('id', PARAM_INT);
$sql1='SELECT s.fullname
        FROM mdl_school s JOIN mdl_course c on s.id=c.school_id
        where c.id='.$courseid;
$resu=$DB->get_records_sql($sql1);

foreach($resu as $sure){
        $str=$sure->fullname;                
    }

$PAGE->set_heading($str);
$PAGE->set_pagelayout('incourse');





global $DB, $USER;

$sql = 'SELECT fullname
        FROM mdl_course 
        WHERE id='.$courseid;

$hd = $DB->get_record_sql($sql);
echo $OUTPUT->header();


echo $OUTPUT->heading("Danh sách học sinh của lớp ".$hd->fullname);

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
    h4{color: purple}
    
    .head{ width: 100%; display: flex; justify-content: space-between;position: relative}
    #excel{
        float: right;
     }
    #excel{margin-bottom: 5px; }
    #hh{margin-bottom: 0px}
</style>";

$quiz_names = $DB->get_records_sql('SELECT DISTINCT name, timeopen FROM {quiz} WHERE course = ? ORDER BY timeopen', array($courseid));

$select_clauses = [];
foreach ($quiz_names as $quiz) {
    $select_clauses[] = "MAX(CASE WHEN q.name = '{$quiz->name}' THEN g.grade END) AS '{$quiz->name}'";
}
$select_clause = implode(", ", $select_clauses);

$sql = "SELECT 
            ROW_NUMBER() OVER (ORDER BY u.username) AS stt,
            u.username,
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
            u.username";

// Execute the query
$grades = $DB->get_records_sql($sql, array($courseid));

// Display the results in a table
$table = new html_table();
$table->head = array('STT','User Name' , 'Họ Tên Học Sinh');
foreach ($quiz_names as $quiz) {
    $table->head[] = $quiz->name;
}
$table->head[] = 'Điểm trung bình';
$table->head[] = 'Học Lực';
$failed_count = 0; 
$passed_count = 0;
$total_grades = []; 
$student_count = 0;
$gioi=0;
$kha=0;
$trungbinh=0;
$yeu=0;
$kem=0;

foreach ($grades as $grade) {
    $fullname = $grade->firstname . ' ' . $grade->lastname;
    $row = array($grade->stt, $grade->username, $fullname);
    $student_total_grade = 0;
    foreach ($quiz_names as $quiz) {
        $diem = number_format($grade->{$quiz->name}, 2, '.', '');
        $row[] = $diem;
        $student_total_grade += $grade->{$quiz->name};

        // Cộng vào tổng điểm của tất cả học sinh (để tính trung bình sau)
        if (!isset($total_grades[$quiz->name])) {
            $total_grades[$quiz->name] = 0;
        }
        $total_grades[$quiz->name] += $grade->{$quiz->name};
    }
    $diemtrungbinh = number_format($grade->average_grade, 2, '.', '');
    $row[] = $diemtrungbinh;
    
    
    if($diemtrungbinh >= 5){
        if($diemtrungbinh<6.5){
            $row[] = 'Trung bình';
            $trungbinh++;
        }
        else if($diemtrungbinh>8){
            $row[] = 'Giỏi';
            $gioi++;
        }
        else if($diemtrungbinh<8){
            $row[] = 'Khá';
            $kha++;    
        }
        $passed_count++;
    } 
    else {
        $failed_count++;
        if($diemtrungbinh<3.5){
            $row[] = 'Kém';
            $kem++;
        }
        else if($diemtrungbinh>3.5){
            $row[] = 'Yếu';
            $yeu++;
        }
    }
    $table->data[] = $row;
    $student_count++;
}
// $row_average = array();
// $row_average[] = 'Trung Bình';  // Ô "Trung Bình" sẽ ở vị trí cột đầu tiên
// $row_average[] = 'N/A';            // Cột thứ hai để trống
// $row_average[] = 'N/A';  
// foreach ($quiz_names as $quiz) {
//     // Tính điểm trung bình cho từng quiz
//     $average_quiz_score = number_format($total_grades[$quiz->name] / $student_count, 2, '.', '');
//     $row_average[] = $average_quiz_score;
// }

// // Tính điểm trung bình của tất cả học sinh (trung bình cộng các điểm trung bình)
// $average_all_students = number_format(array_sum(array_column($grades, 'average_grade')) / $student_count, 2, '.', '');
// $row_average[] = $average_all_students;

// // Thêm dòng trung bình vào bảng
// $table->data[] = $row_average;

foreach ($table->data as $rowindex => $row) {
    foreach ($row as $colindex => $cell) {
        // Kiểm tra nếu cột không phải là cột thứ 2 (index 1)
        if ($colindex != 2) {
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

$sql = 'SELECT u.firstname, u.lastname
        FROM mdl_user u
        JOIN mdl_role_assignments ra ON u.id = ra.userid
        JOIN mdl_context ct ON ct.id = ra.contextid
        JOIN mdl_course c ON c.id = ct.instanceid
        WHERE c.id = '.$courseid.' AND ra.roleid =(SELECT id from mdl_role WHERE shortname = "editingteacher");';

$teacher = $DB->get_record_sql($sql);
$table1 = new html_table();
$teachername =$teacher->firstname.' '.$teacher->lastname;
$table1->head=array('Tên Giáo Viên Phụ Trách:'.'- '.$teachername.' -', );


$table2 = new html_table();
$table2->head=array('STT', 'Tên Học Sinh', 'Username', 'Email');
$counter = 1;
foreach($rs as $student){
    $fullname = $student->firstname.' '.$student->lastname;
    $row = array($counter++, $fullname, $student->username, $student->email);
    $table2->data[]=$row;                
}

//echo html_writer::table($table1);

//echo '<table class="abc"><tr><th>Tên Giáo Viên: </th> <th>'.$teachername.' </th></tr></table>';
echo '<div class="head" >';
echo '<h4 id="hh">  - Giáo Viên Phụ Trách: '.$teachername.' - </h4>';
$link = new moodle_url('http://localhost/moodle/blocks/online_users/bieudo.php', array('id' => $courseid));
echo html_writer::link($link, 'Xem biểu đồ', array('class' => 'btn btn-primary', 'id' => 'excel'));
echo '</div>';
echo html_writer::table($table);

echo '<i style="font-size: 18px">Số học sinh qua môn: '.$passed_count .' | Số học sinh rớt môn: '.$failed_count.'</i>';

echo '<h2 style="margin-top: 80px">Thống kê học lực lớp '.$hd->fullname.'</h2>';

echo '<div class="duoi"><table class="nhangu">
        <tr>
            <th>Học Lực</th>
            <td>Giỏi</td>
            <td>Khá</td>
            <td>Trung Bình</td>
            <td>Yếu</td>
            <td>Kém</td>       
        </tr>';
echo '  <tr>
            <th>Số Lượng</th>
            <td>' . $gioi . '</td>       
            <td>' . $kha . '</td> 
            <td>' . $trungbinh . '</td> 
            <td>' . $yeu . '</td> 
            <td>' . $kem . '</td> 
        </tr>
    </table>';
    $total = $gioi + $kha + $trungbinh + $yeu + $kem;
    $data = [
        'Giỏi' => $gioi,
        'Khá' => $kha,
        'Trung Bình' => $trungbinh,
        'Yếu' => $yeu,
        'Kém' => $kem
    ];
    $percentages = array_map(function($count) use ($total) { 
        return round(($count / $total) * 100, 2); 
    }, array_values($data));
    // Hiển thị bảng và biểu đồ
    
    // Thêm phần tử canvas để vẽ biểu đồ
    echo '<div><canvas id="academicChart" ></canvas></div></div>';
    
    // Thêm mã JavaScript để vẽ biểu đồ
    echo '<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>';
    echo '<style>
            #academicChart {
                width: 300px !important;
                height: 300px !important;
                padding: 0px !important;
                margin-left: 50px;
            }
        </style>';

// Thêm mã JavaScript để vẽ biểu đồ và hiển thị phần trăm
echo '<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>';
echo '<script>
    document.addEventListener("DOMContentLoaded", function() {
        const data = ' . json_encode(array_values($data)) . ';
        const labels = ' . json_encode(array_keys($data)) . ';
        const percentages = ' . json_encode($percentages) . ';
        const ctx = document.getElementById("academicChart").getContext("2d");
        new Chart(ctx, {
            type: "pie",
            data: {
                labels: labels,
                datasets: [{
                    label: "Thống kê học lực",
                    data: data,
                    backgroundColor: [
                        "rgba(75, 192, 192, 0.2)",
                        "rgba(255, 206, 86, 0.2)",
                        "rgba(255, 99, 132, 0.2)",
                        "rgba(153, 102, 255, 0.2)",
                        "rgba(255, 159, 64, 0.2)"
                    ],
                    borderColor: [
                        "rgba(75, 192, 192, 1)",
                        "rgba(255, 206, 86, 1)",
                        "rgba(255, 99, 132, 1)",
                        "rgba(153, 102, 255, 1)",
                        "rgba(255, 159, 64, 1)"
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 0,
                        right: 0,
                        top: 0,
                        bottom: 0
                    }
                },
                plugins: {
                    legend: {
                        position: "top",
                    },
                    tooltip: {
                        enabled: true
                    },
                    datalabels: {
                        formatter: (value, ctx) => {
                            const percentage = percentages[ctx.dataIndex];
                            return percentage + "%";
                        },
                        color: "#5069c7",
                        font: {
                            weight: "bold",
                            size: 25
                        },
                        align: "center",
                        anchor: "center"
                    }
                }
            },
            plugins: [ChartDataLabels]
        });
    });
</script>';
    

echo '<style>
    .nhangu {
        width: 60%;
        border-collapse: collapse;
        font-family: Arial, sans-serif;
        margin-top: 20px;
        font-size: 18px;
        height: 20%;
        position:absolution;
        top: 50%;
        margin-top: 100px;
    }

    .nhangu th, .nhangu td {
        padding: 8px;
        text-align: center;
        border: 1px solid #707070;
        width: 10%; /* Đảm bảo mỗi cột có độ rộng bằng nhau */
    }

    .nhangu th {
        background-color: #d6d6d6;
        font-weight: bold;
    }

    .nhangu td {
        background-color: #fff;
    }

    .nhangu tr:nth-child(odd) td {
        background-color: #ededed;
    }

    .duoi {width: 100%; display: flex; height: 400px; position:relative; margin-top: 10px; justify-content: center}
</style>';

echo $OUTPUT->footer();


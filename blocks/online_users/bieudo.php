<?php

require_once '../../config.php';


// require_login();
$context = context_course::instance($COURSE->id);



$PAGE->set_url('/local/blocks/online_users/bieudo.php');
$PAGE->set_context(context_course::instance($COURSE->id));

global $DB, $USER;

$courseid = required_param('id', PARAM_INT);
$sql1='SELECT fullname
        FROM mdl_course 
        where id='.$courseid;
$resu=$DB->get_records_sql($sql1);

foreach($resu as $sure){
        $str=$sure->fullname;                
    }

$PAGE->set_heading($str);
$PAGE->set_pagelayout('incourse');
echo $OUTPUT->header();


echo $OUTPUT->heading("Biểu đồ kết quả học tập của lớp ".$str);

$kem=0;
$yeu=0;
$trungbinh=0;
$kha=0;
$gioi=0;


$sql = 'SELECT u.id AS userid, u.firstname, u.lastname, AVG(gg.finalgrade) AS avggrade
                FROM {user} u
                JOIN {grade_items} gi ON gi.courseid ='.$courseid.'
                JOIN {grade_grades} gg ON gg.itemid = gi.id AND gg.userid = u.id
                WHERE gi.itemmodule IN ("quiz")  
                AND gi.courseid = '.$courseid.' AND u.id!=2
                GROUP BY u.id';
        $students = $DB->get_records_sql($sql);

        $student_data = [];
        foreach ($students as $student) {
            // Nếu học sinh chưa có điểm, gán điểm 0
            $grade = is_null($student->avggrade) ? 0 : $student->avggrade;
            $student_data[] = [
                'name' => $student->firstname . ' ' . $student->lastname,
                'grade' => $grade
            ];
            if($student->avggrade>=8){
                $gioi++;
            }
            else if($student->avggrade>=6.5){
                $kha++;
            }
            else if($student->avggrade>=5){
                $trungbinh++;
            }
            else if($student->avggrade>=3.5){
                $yeu++;
            }
            else {
                $kem++;
            }
        }

        // Chuyển dữ liệu thành chuỗi JSON
        $student_json = json_encode($student_data);

        // Tạo nội dung cho block (dựng biểu đồ)
        echo '<div id="chart-container" style="width: 90%; height: 90%;">
                <canvas id="myChart"></canvas>
            </div>';
        //echo '<h2 style="margin-bottom: 20px; margin-top: 50px">Thống kê học lực lớp '.$str.'</h2>';
        echo '<table>
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
                    <td>'.$gioi.'</td>       
                    <td>'.$kha.'</td> 
                    <td>'.$trungbinh.'</td> 
                    <td>'.$yeu.'</td> 
                    <td>'.$kem.'</td> 
                </tr>
            ';
        echo '</table>';
        echo'<style>
            table {
                width: 70%;
                border-collapse: collapse;
                font-family: Arial, sans-serif;
                margin-left: 160px;
                margin-top: 20px;
                font-size: 18px;
            }

            th, td {
                padding: 8px;
                text-align: center;
                border: 1px solid #707070;
                width: 10%; /* Đảm bảo mỗi cột có độ rộng bằng nhau */
            }

            th {
                background-color: #d6d6d6;
                font-weight: bold;
            }

            td {
                background-color: #fff;
            }

            tr:nth-child(odd) td {
                background-color: #ededed;
            }

        </style>';
        // Thêm mã JavaScript để tạo biểu đồ
        echo '
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                var ctx = document.getElementById("myChart").getContext("2d");
                var studentData = ' . $student_json . ';
                
                var labels = studentData.map(function(student) { return student.name; });
                var grades = studentData.map(function(student) { return student.grade; });

                new Chart(ctx, {
                    type: "bar",
                    data: {
                        labels: labels,
                        datasets: [{
                            label: "Điểm trung bình",
                            data: grades,
                            backgroundColor: "rgba(75, 192, 192, 0.2)",
                            borderColor: "rgba(75, 192, 192, 1)",
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 10, // Giới hạn điểm (nếu cần thiết)
                                title: {
                                    display: true,
                                    text: "Điểm"
                                }
                            }
                        }
                    }
                });
            </script>
        ';


echo $OUTPUT->footer();

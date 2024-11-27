<?php
require_once('../../config.php');
require_once($CFG->dirroot.'/enrol/importexcel/lib.php');
require_login();
// require_once($CFG->libdir . '/phpexcel/PHPExcel.php');
// require_once($CFG->libdir . '/phpexcel/PHPExcel/IOFactory.php');
require_once($CFG->libdir . '/phpspreadsheet/vendor/autoload.php');

$courseid = intval($_GET['id']);

$PAGE->set_url(new moodle_url('/local/enrol/addtocourse/index.php', array('id' => $courseid)));
$PAGE->set_context(context_system::instance());
$PAGE->set_title('Thêm học sinh');
//$PAGE->set_heading();

echo $OUTPUT->header();

global $DB, $USER, $COURSE;
$course = $DB->get_record('course', array('id' => $courseid));

echo $OUTPUT->heading('Upload file Excel để thêm học sinh vào khóa học '.$course->fullname);

// echo'<br><br><br>';


echo '<form class="custom-form" method="post" enctype="multipart/form-data">
        <label for="courseid">Chọn file định dạng Excel:</label>
        
        <input type="file" name="studentfile">
        <button type="submit">Upload</button>
      </form>';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['studentfile'])) {
    $filepath = $_FILES['studentfile']['tmp_name'];
    $filetype = \PhpOffice\PhpSpreadsheet\IOFactory::identify($filepath);
    $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($filetype);
    $spreadsheet = $reader->load($filepath);

    $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);


    foreach ($sheetData as $row) {
        $username = $row['A']; // Assume username is in column A

        // Enroll student in the course
        enroll_student_in_course($username, $courseid);
    }

    echo '<h4>Đã cập nhật danh sách thành công!</h4><br>';
    echo '<div class="uploadexcel"><a class="thea" style="background-color: #0073aa; color: #fff; border: none; cursor: pointer; padding: 10px; border-radius: 10px" href="http://localhost/moodle/course/view.php?id='.$courseid.'">←Trở lại khóa học </a></div>';
}
echo '<style> 
        .custom-form { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; background: #f9f9f9; } 
        .custom-form label { display: block; margin-bottom: 8px; font-weight: bold; } 
        .custom-form select, .custom-form input[type="file"], .custom-form button { width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 4px; } 
        .custom-form button { background-color: #0073aa; color: #fff; border: none; cursor: pointer; } 
        .custom-form button:hover { background-color: #005a8c; } 
        h4 {text-align: center; }
        h6{text-align: center; color: red}
        p {color: blue; text-align: center}
        h1 { text-align: center; color: #333; margin-bottom: 20px; font-size: 2.5em; font-weight: bolder} 
        h2 { text-align: center; color: #555; margin-bottom: 15px; font-size: 2em; font-weight: bolder}
        h5 {text-align: center}
        .uploadexcel{display: flex; justify-content: center}
        .thea {max-width: 300px; width: 200px; text-align: center}
    </style>';


echo $OUTPUT->footer();
?>

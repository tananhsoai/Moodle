<?php
require_once('../../config.php');
require_once($CFG->libdir . '/formslib.php');

// Kiểm tra quyền truy cập của người dùng
require_login();

// Lớp để tạo form
class school_form extends moodleform {
    // Định nghĩa form
    public function definition() {
        $mform = $this->_form;

        // Tạo trường nhập tên trường
        $mform->addElement('text', 'fullname', 'Nhập tên trường: ', array('size' => '50'));
        $mform->setType('fullname', PARAM_TEXT);

        // // Tạo trường nhập địa chỉ trường
        // $mform->addElement('text', 'address', get_string('address', 'local_myplugin'), array('size' => '50'));
        // $mform->setType('address', PARAM_TEXT);

        // // Tạo trường nhập email
        // $mform->addElement('text', 'email', get_string('email', 'local_myplugin'), array('size' => '50'));
        // $mform->setType('email', PARAM_EMAIL);

        // // Tạo trường nhập số điện thoại
        // $mform->addElement('text', 'phone', get_string('phone', 'local_myplugin'), array('size' => '50'));
        // $mform->setType('phone', PARAM_TEXT);

        // Thêm nút Submit
        $this->add_action_buttons(true, 'Thêm');
    }
}

// Xử lý khi form được gửi
if ($form = new school_form()) {
    if ($form->is_cancelled()) {
        redirect(new moodle_url('/'));  // Redirect nếu form bị hủy
    } else if ($data = $form->get_data()) {
        // Nếu form được gửi và dữ liệu hợp lệ, xử lý dữ liệu
        global $DB;

        // Tạo đối tượng chứa dữ liệu
        $school = new stdClass();
        $school->fullname = $data->fullname;
        // $school->address = $data->address;
        // $school->email = $data->email;
        // $school->phone = $data->phone;

        // Chèn vào bảng 'schools'
        $DB->insert_record('school', $school);

        // Thông báo thành công
        // echo $OUTPUT->notification('Da them truong thanh cong', 'notifysuccess');
        // echo '<a href="http://localhost/moodle/my">Tro ve trang chu</a>';

        redirect(new moodle_url('/my'), 'Đã thêm một trường thành công!', null, \core\output\notification::NOTIFY_SUCCESS);
    } else {
        // Nếu form chưa được gửi hoặc có lỗi
        echo '<div class="overley">';
        $form->display();
        echo '</div>';
    }
}
// echo '<style>
//     /* Tạo khoảng cách giữa các trường trong form */
//     form.mform {
//         margin: 20px;
//         padding: 20px;
//         border: 1px solid #ccc;
//         background-color: #f9f9f9;
//         border-radius: 5px;
//     }

//     .mform input[type="text"],
//     .mform textarea {
//         width: 100%;
//         padding: 10px;
//         margin: 5px 0 15px 0;
//         border: 1px solid #ccc;
//         border-radius: 4px;
//         box-sizing: border-box;
//     }

//     .mform textarea {
//         resize: vertical;
//     }

//     .mform .formsubmit {
//         background-color: #4CAF50;
//         color: white;
//         padding: 10px 15px;
//         border: none;
//         border-radius: 4px;
//         cursor: pointer;
//     }

//     .mform .formsubmit:hover {
//         background-color: #45a049;
//     }

//     /* Thêm hiệu ứng hover cho các trường nhập */
//     .mform input[type="text"]:focus,
//     .mform textarea:focus {
//         border-color: #4CAF50;
//         outline: none;
//     }

// </style>';

echo '<style>
    .overley{
        position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); /* Làm mờ nội dung phía sau */ z-index: 1; /* Đảm bảo lớp phủ nằm trên cùng */
    }
    body {
        background: rgba(0, 0, 0, 0.5);
        background: url("./img/background.png") no-repeat center center fixed; 
        background-size: cover; /* Đảm bảo hình nền bao phủ toàn bộ trang */
    }
    /* CSS cho toàn bộ form */
    .mform {
        max-width: 500px;
        position: fixed; 
        top: 30%; 
        left: 50%; 
        transform: translate(-50%, -50%); 
        z-index: 2; /* Đảm bảo form nằm trên overlay */
        padding: 20px;
        background-color: #f9f9f9;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    /* Định dạng các trường text input */
    .mform input[type="text"] {
        width: 100%;
        padding: 10px;
        margin: 10px 0;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
        font-size: 16px;
    }

    /* Định dạng nhãn (label) */
    .mform label {
        font-weight: bold;
        margin-bottom: 5px;
        display: block;
    }

    /* Định dạng nút submit */
    /* Tùy chỉnh cho nút submit sử dụng lớp btn */
    .mform .btn {
        background-color: #007bff; /* Màu xanh dương */
        color: white;
        padding: 12px 25px; /* Kích thước padding */
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        text-align: center;
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    /* Hiệu ứng hover khi rê chuột lên nút */
    .mform .btn:hover {
        background-color: #0056b3;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        transform: translateY(-3px);
    }

    /* Hiệu ứng khi nút được focus */
    .mform .btn:focus {
        outline: none;
        box-shadow: 0 0 8px rgba(0, 123, 255, 0.6);
    }

    /* Hiệu ứng khi nút được nhấn */
    .mform .btn:active {
        background-color: #004085;
        transform: translateY(2px);
    }

    .mform .fitem {
        display: inline-block; /* Hiển thị các fitem ngang hàng */
        margin-right: 15px; /* Khoảng cách giữa các nút */
    }
    .mform .fitem {            
        justify-content: center;    /* Căn giữa các nút theo chiều ngang */
        align-items: center;        /* Căn giữa các nút theo chiều dọc */
        gap: 15px;                  /* Khoảng cách giữa các nút */
    }



    .mformerrortext {
        color: red;
        font-weight: bold;
    }

    /* Thêm khoảng cách giữa các phần tử */
    .mform .fitem {
        margin-bottom: 15px;
    }



</style>';
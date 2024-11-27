<?php
require_once('../../config.php');

// Kiểm tra quyền truy cập
require_login();


// Lấy ID của trường học cần xóa
$school_id = required_param('id', PARAM_INT);

// Xóa trường học
$DB->delete_records('school', array('id' => $school_id));

// Thông báo thành công và quay lại trang trước
redirect(new moodle_url('/my'), 'Đã xóa một trường thành công!', null, \core\output\notification::NOTIFY_SUCCESS);


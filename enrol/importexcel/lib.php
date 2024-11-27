<?php

// class enrol_addtocourse_plugin extends enrol_plugin {
    
   
//     public function allow_enrol($instance) {
//         return true;
//     }

    
//     public function use_standard_editing_ui() {
//         return true;
//     }

   
//     public function edit_instance_form($instance, MoodleQuickForm $mform, $context) {
      
//     }

    
//     public function edit_instance_validation($data, $files, $instance, $context) {
       
//         debugging('enrol_plugin::edit_instance_validation() is missing. This plugin has no validation!', DEBUG_DEVELOPER);
//         return array();
//     }

    
//     public function can_add_instance($courseid) {
//         return true;
//     }
//     public function enroll_student_in_course($username, $courseid) {
//         global $DB, $CFG;
//         require_once($CFG->dirroot . '/enrol/manual/lib.php');
    

//         if ($user = $DB->get_record('user', ['username' => $username])) {
          
//             $enrol = enrol_get_plugin('manual');
    
            
//             $context = context_course::instance($courseid);
    
//             $enrol->enrol_user($instance, $user->id, 5); // 5 is the role ID for student
//             echo "User $username enrolled successfully.<br>";
//         } else {
//             echo "User $username not found.<br>";
//         }
//     }
//     public function enrol_addtocourse_extend_navigation(global_navigation $navigation) { 
        

//         $node = $navigation->add( 
//             get_string('pluginname', 'enrol_addtocourse'), 
//             new moodle_url('/local/enrol/addtocourse/index.php') // Link to your plugin's index.php 
//             ); 
//         $node->showinflatnavigation = true;
//     }
    
// } 
defined('MOODLE_INTERNAL') || die();

function enroll_student_in_course($username, $courseid) {
    global $DB, $CFG;
    require_once($CFG->dirroot . '/enrol/manual/lib.php');

    // Find user by username
    if ($user = $DB->get_record('user', ['username' => $username])) {
        // Get the manual enrolment plugin
        $enrol = enrol_get_plugin('manual');

        // Get the course context
        $context = context_course::instance($courseid);

        // Enroll the user
        $instance = $DB->get_record('enrol', array('courseid'=>$courseid, 'enrol'=>'manual'), '*', MUST_EXIST);
        $enrol->enrol_user($instance, $user->id, 5); // 5 is the role ID for student
        echo "<p>Người dùng $username đã được thêm vào khóa học!</p>";
    } else {
        echo "<h6>Người dùng $username không tồn tại!</h6>";
    }
}

function enrol_addtocourse_extend_navigation(global_navigation $navigation) {
    $node = $navigation->add(get_string('pluginname', 'enrol_addtocourse'), new moodle_url('/local/enrol/importexcel/index.php', array('id' => $course->id)));
    $node->showinflatnavigation = true;
}
 ?>


<?php

defined('MOODLE_INTERNAL') || die();

use core_user\fields;


function diemkt_report_extend_navigation($reportnav, $course, $context) {
    $url = new moodle_url('/course/report/diemkt/index.php', array('id' => $course->id));
    $reportnav->add(get_string('pluginname', 'coursereport_diemkt'), $url);
}


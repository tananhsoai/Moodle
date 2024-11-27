<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Block listmycourses is defined here.
 *
 * @package     block_listmycourses
 * @copyright   2024 Your Name <you@example.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_listmycourses extends block_base {

    /**
     * Initializes class member variables.
     */
    public function init() {
        // Needed by Moodle to differentiate between blocks.
        $this->title = get_string('pluginname', 'block_listmycourses');
    }

    /**
     * Returns the block contents.
     *
     * @return stdClass The block contents.
     */
    public function get_content() {
        global $DB, $USER, $CFG;
        if ($this->content !== null) {
            return $this->content;
        }

        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';

        if (!empty($this->config->text)) {
            $this->content->text = $this->config->text;
        } else {
            // $text = "<h6>".get_string('pluginname_display', 'block_listmycourses')."</h6>";
            // $text="<h6>Courses List</h6>";
            $sql = 'SELECT c.fullname, u.firstname, u.lastname, u.id AS user_id, c.id AS cid
                        FROM mdl_user u
                        JOIN mdl_role_assignments ra ON u.id = ra.userid
                        JOIN mdl_context ctx ON ra.contextid = ctx.id
                        JOIN mdl_course c ON ctx.instanceid = c.id
                        JOIN ( 
                            SELECT ctx.instanceid, ctx.id 
                            FROM mdl_context ctx 
                            JOIN mdl_role_assignments ra 
                            ON ra.contextid = ctx.id 
                            WHERE ra.userid = '.$USER->id.'
                        ) AS enrolled_courses ON enrolled_courses.instanceid = c.id
                        WHERE c.id !=0 AND ra.roleid = (SELECT id FROM mdl_role WHERE shortname = "editingteacher");';
            $result = $DB->get_records_sql($sql);
            $table = "<style>    
                td, th {padding: 1px}
                table, td, th {border: 1px solid #dee2e6}
                tbody{border: 1px solid #ccc}
                table {border-collapse: separate; margin-bottom: 2em; height: 100%;}
                table {width: 250px}
                th {height: 10px}
                td{white-space: nowrap; vertical-align: middle}
                table {font-size: 12px}
                .cuon{width: 100%; height: 120px; overflow: hidden; overflow-y : scroll}
                .cc{text-align:center;}
                .mde{color: black}
            </style>";
            $table .= '<div class= "cuon" ><table> <tbody>';
            $table .= "<tr>
                <th>STT</th>
                <th>Course name</th>
                <th>Teacher</th>
                </tr>";
            $counter = 1;
            foreach($result as $course){

                $giangvien = $course->firstname . ' ' . $course->lastname;
                $table .= '<tr>
                <td class="cc">'.$counter++.'</td>
                <td>'.'<a class="mde" href="http://localhost/moodle/course/view.php?id='.$course->cid.'">'.$course->fullname.'</a></td>
                <td>'.'<a class="mde" href="http://localhost/moodle/user/profile.php?id='.$course->user_id.'">'.$giangvien."</a></td>
                </tr>";
                
            }
            $table .="</tbody> </table></div>";
            $text = $table;


            $this->content->text = $text;
        }

        return $this->content;
    }

    /**
     * Defines configuration data.
     *
     * The function is called immediately after init().
     */
    public function specialization() {

        // Load user defined title and make sure it's never empty.
        if (empty($this->config->title)) {
            $this->title = get_string('pluginname', 'block_listmycourses');
        } else {
            $this->title = $this->config->title;
        }
    }

    /**
     * Enables global configuration of the block in settings.php.
     *
     * @return bool True if the global configuration is enabled.
     */
    public function has_config() {
        return true;
    }

    /**
     * Sets the applicable formats for the block.
     *
     * @return string[] Array of pages and permissions.
     */
    public function applicable_formats() {
        return array(
            'all'=>true
        );
    }
    function self_test(){
        return true;
    }
}

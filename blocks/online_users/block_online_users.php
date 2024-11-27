<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Online users block.
 *
 * @package    block_online_users
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */



/**
 * This block needs to be reworked.
 * The new roles system does away with the concepts of rigid student and
 * teacher roles.
 */
class block_online_users extends block_base {
    function init() {
        $this->title = 'List Schools';
    }

    function has_config() {
        return true;
    }

    function get_content() {
        global $USER, $CFG, $DB, $OUTPUT;

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';

        if (empty($this->instance)) {
            return $this->content;
        }

        if (!empty($this->config->text)) {
            $this->content->text = $this->config->text;
        } else {
            global $DB, $OUTPUT;

            $schools= $DB->get_records('school');
            // $deleteurl = '/blocks/listschool/delete_school.php';
            $text = '<style>
                .abc{border-radius: 10px solid blue}
                
                .abc {
                    display: flex; 
                    justify-content: flex-start; 
                    gap: 15px; 
                }

                .abc {
                    display: inline-block;
                    background-color: #007bff; 
                    color: white;
                    padding: 7px 7px; 
                    border: none; 
                    border-radius: 8px; 
                    font-size: 16px; 
                    font-weight: bold;
                    cursor: pointer; 
                    transition: background-color 0.3s ease, box-shadow 0.3s ease;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); 
                    transition: transform 0.2s ease-in-out, background-color 0.2s ease-in-out;
                }
                .abc:hover {
                    background-color: #0056b3;
                    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
                    transform: translatex(10px); 
                    transform: translateY(-5px); 
                }

                .ab{border-radius: 5px solid blue}
                

                .ab {
                    margin-inline-end: 360px;
                    position: fixed;
                    left: 690px;
                    direction: rtl;
                    display: inline-block;
                    color: black;
                    padding: 1px 10px; 
                    border: none; 
                    border-radius: 8px; 
                    font-size: 16px; 
                    font-weight: bold;
                    cursor: pointer; 
                    transition: background-color 0.3s ease, box-shadow 0.3s ease;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); 
                    transition: transform 0.2s ease-in-out, background-color 0.2s ease-in-out;
                }
                .ab:hover {
                    background-color: #ff0000;
                    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
                    transform: translatex(10px); 
                    transform: translateY(-5px); 
                }
                .bc{font-size: 20px; color: black; text-decoration: none;}
                li::marker {
                    font-size: 20px;  
                    //color: #ff5733;   
                }
                li{margin: 10px;}
            </style>';
            $action = new component_action('moodle', 'action'); 
            $attributes = array('class' => 'ab');
            $text .= '<ol>';
            foreach($schools as $school){
                $text .= '<li><a class="bc" href="http://localhost/moodle/blocks/online_users/classes.php?id='.$school->id.'">'.$school->fullname.'</a>'
                .' '.$OUTPUT->action_link('/blocks/online_users/delete_school.php?id='.$school->id, '❌', $action, $attributes).
                '</li>';
            }
            $text .= '<a class="abc" href="http://localhost/moodle/blocks/online_users/school_form.php">➕</a>';
            $text .= '</ol>';
            $this->content->text = $text;
        }

        return $this->content;
        
    }
}



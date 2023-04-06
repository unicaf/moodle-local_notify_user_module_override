<?php
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
 * Version info
 *
 * @package    local_course_reminder
 * @copyright  2023 UNICAF LTD <info@unicaf.org>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class checkStatusClass
{

    function checkStatus($courseid){
        $this -> courseid = $courseid;
        global $DB;
        if
        ($check_if_enabled = $DB->record_exists("local_course_reminder",["courseid"=>"$courseid"])){
        }else{

            $this->add_to_table();
        }
        echo "Hello world ";
        echo "If you see this it works";


    }

    function add_to_table(){
        global $DB;
        $record = new stdClass();
        $record->courseid = $this->courseid;
        $DB->insert_record('local_course_reminder',$record,false);


    }



}
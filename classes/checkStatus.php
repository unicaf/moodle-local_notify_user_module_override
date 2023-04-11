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
        if ($DB->record_exists("local_course_reminder",["courseid"=>"$courseid"])){
        }else{
            $this->add_to_table();
        }


    }

    function add_to_table(){
        global $DB;
        $record = new stdClass();
        $record->courseid = $this->courseid;
        $DB->insert_record('local_course_reminder',$record,false);


    }

    function set_enable($fromform){
        global $DB;

        $record = new stdClass();
       $this->enable = $fromform->enable;

       var_dump($this);
       $DB->update_record('local_course_reminder',$record);


    }

    function get_id_table(){
        global $DB;
        $table_id = $DB->get_record('local_course_reminder',['courseid' => $this->courseid],'id');
//        var_dump($this);
//        var_dump($table_id);
        return $table_id;

    }
     //TODO GET RECORD FROM DATABASE ID





}
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
    function __construct($courseid){
//        echo "This is the new Course id from constructor ($courseid)";
        $this->courseid = $courseid;
        $this->tableID = $this->get_id_table();

//        var_dump($this);
    }

    function checkStatus(){
        global $DB;
//        $this -> courseid = $courseid;

//        $this->tableid = $this->get_id_table();
        $this->isEnabled = $this->is_enabled();
        $recordExisits = $DB->record_exists("local_course_reminder",["courseid"=>"$this->courseid"]);
        if ($recordExisits){

        }else{
            $this->add_to_table();
        }


    }

    function add_to_table(){
        // if record is not in database its adds it
        global $DB;
        $record = new stdClass();
        $record->courseid = $this->courseid;
        $DB->insert_record('local_course_reminder',$record,false);


    }

    function set_enable($fromform){

        // Sets field enable to 0 or 1 within the coursesettings.php
        global $DB;

        $record1 = new stdClass();

        $record1->enable = $fromform->enable;
        $record1 -> id = $this->tableID->id;

       $DB->update_record('local_course_reminder', $record1);




    }

    function is_enabled(){
        //Gets enable field from database on table
        global $DB;
        $is_enabled = $DB->get_record('local_course_reminder', ['courseid' => $this->courseid], 'enable');
        return $is_enabled;
    }

    function get_id_table(){
        //Gets id of instance for the coruseid
        global $DB;
        $table_id = $DB->get_record('local_course_reminder',['courseid' => $this->courseid],'id');
//        var_dump($this);
//        var_dump($table_id);
        return $table_id;

    }






}
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
namespace local_course_reminder;
defined('MOODLE_INTERNAL') || die();
require __DIR__.'/emails.php';
require_once($CFG->dirroot ."/config.php");




use core\event\assessable_submitted;
use \mod_assign\event\user_override_created;
function getData($event)
{
    // Defines $COURSE object
    global $COURSE;

    $courseObject = $COURSE;
    $event_data = $event->get_data();
    //related user is the user which is affected - student
    $relatedStudent = $event_data["relateduserid"];

//    Gets email of student
    $emailofUser= \core_user::get_user($relatedStudent);

    //Course ID
//    $courseID = $event_data["courseid"];
    $courseID = $courseObject->id;

    // Course NAME
    $courseName = $courseObject->fullname;
    // Either mod_assign or mod_quiz
    $component = $event_data["component"];
    // This is passed to create the correct URL for the email
    $contextinstanceid = $event_data["contextinstanceid"];

    if($component === "mod_assign"){
        $assignId = $event_data["other"]["assignid"];
        $assignment_url = get_assignment_url($contextinstanceid,$component);
        $assignmentName = getAssignmentName($assignId, $table="assign");
        //Assignment Date
        $assignmentDate = getAssignmentDate($assignId,$table="assign");

        //Assignment Override Date
        $assignmentOverrideDate = getAssignmentOverrideDate($assignId,$table="assign_overrides",$relatedStudent);
//      var_dump("Orignal date is ".$assignmentDate->duedate . " the new date override is " . $assignmentOverrideDate->duedate);
//        var_dump(get_teacher());
//        die();
        $assignmentDate = $assignmentDate->duedate;
        $assignmentDate = date('d-M-Y H:i', $assignmentDate);

        $assignmentOverrideDate = $assignmentOverrideDate->duedate;
        $assignmentOverrideDate = date('d-M-Y H:i', $assignmentOverrideDate);


        $component = "Assignment";
    }elseif($component=="mod_quiz"){
        $assignId = $event_data["other"]["quizid"];
        $assignment_url = get_assignment_url($contextinstanceid,$component);
        $assignmentName = getAssignmentName($assignId, $table="quiz");
        $component = "Quiz";
    }else{
        //create error message
        $component = "Assignment / Quiz";
    }


//    getAssignmentName($assignId);
    overrideAssignEmailStudent($emailofUser, $courseID,$courseName, $component, $assignmentName,$assignmentDate,$assignmentOverrideDate,$assignment_url);

}

//Gets the name of the assignment/quiz
function getAssignmentName($id,$table){
    /* In this function we require two parameters , $id and $table. The ID is the ID of the assignment or quiz
    and the table is for the database table (mdl_assign is for assignments and mdl_quiz for quizes)
    We then create a database connection and add our $table parameter passed from getData and return back
    an object with the name of the assignment/quiz
    */
    global $DB;
    $assignmentName = $DB->get_record("$table",array('id'=>$id),'name');
//    var_dump($assignmentName);
    return $assignmentName;
}

function getAssignmentDate($id, $table){
    global $DB;

    $assignmentDate = $DB->get_record("$table", array('id' =>$id), 'duedate');
    return $assignmentDate;
}

function getAssignmentOverrideDate($id, $table, $relatedStudent){
    global $DB;

//    $assignmentDate = $DB->get_record("$table",array('assignid' => $id),'duedate');
    $assignmentDate = $DB->get_record("$table",array('assignid' => $id,"userid"=>$relatedStudent),'duedate');

    return $assignmentDate;
}

//Creates URL link for assignment
function get_assignment_url($contextinstanceid, $component){
    if($component == "mod_assign") {
        return new \moodle_url('/mod/assign/view.php', array('id'=> $contextinstanceid));
    }elseif ($component == "mod_quiz"){
        return new \moodle_url('/mod/quiz/view.php',array('id'=> $contextinstanceid));
    }
}


//function get_group(){
//    global $DB;
//
//
//}


//function get_teacher(){
//    global $DB, $COURSE;
//    $teacher = $DB->get_field("role","id", array("archetype" => "editingteacher"));
//    $courseID = $COURSE->id;
//    $groupid = $DB->get_field("groups","id",array("courseid"=>$courseID));
//    print_r("Group ID is ".$groupid);
//
//    $sql = "SELECT courseid, name FROM mdl_groups INNER JOIN mdl_course ON mdl_groups.courseid = mdl_course.id; ";
//    $run_sql = $DB->get_records_sql($sql);
//    var_dump($run_sql);
//

//    return $teacher;


//}


//TODO Enable and disable through course settings whole functionality


class event_handler
{

    public static function assign_user_override_created(\mod_assign\event\user_override_created $event)
    {


        return getData($event);
    }
    public static function assign_user_override_updated(){

    }

    public static function quiz_user_override_created(\mod_quiz\event\user_override_created $event)
    {
        return getData($event);
    }
    public static function quiz_user_override_updated()
    {

    }


}





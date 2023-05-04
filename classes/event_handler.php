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
//require_once($CFG->dirroot ."/config.php");
require __DIR__.'/checkStatus.php';




use core\event\assessable_submitted;
use core_analytics\course;
use \mod_assign\event\user_override_created;
function getData($event)
{


    // Defines $COURSE object
    global $COURSE;
    $courseObject = $COURSE;
    $event_data = $event->get_data();
    $courseID = $courseObject->id;
    //Declares the class
    $add_to_table_reminders = new \checkStatusClass($courseID);
    $who_to_send = new \checkStatusClass($courseID);

//    var_dump($add_to_table_reminders);
    // Adds to table 
    $add_to_table_reminders->checkStatus();

    $is_enabled = $add_to_table_reminders->is_enabled();
    $is_enabled = $is_enabled->enable;


    //related user is the user which is affected - student
    $relatedStudent = $event_data["relateduserid"];

//    Gets email of student
    $emailofUser= \core_user::get_user($relatedStudent);

    //Course ID
//    $courseID = $event_data["courseid"];


    // Course NAME
    $courseName = $courseObject->fullname;

    // Either mod_assign or mod_quiz
    $component = $event_data["component"];
    // This is passed to create the correct URL for the email
    $contextinstanceid = $event_data["contextinstanceid"];


    // This checks to proceed with the script if the enable field in local_course_reminder table is set to 1 (enabled)


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



        $assignmentOverrideDate = $assignmentOverrideDate->duedate;



        $component = "assignment";
    }elseif($component=="mod_quiz"){
        $assignId = $event_data["other"]["quizid"];
        $assignment_url = get_assignment_url($contextinstanceid,$component);
        $assignmentName = getAssignmentName($assignId, $table="quiz");
        $component = "quiz";
    }else{
        //create error message
        $component = "assignment / quiz";
    }


    $who_to_send->who_to_send_notification($emailofUser,$courseName, $component, $assignmentName,$assignId,$assignmentDate,$assignmentOverrideDate,$assignment_url,$contextinstanceid);

    if(!$is_enabled == "1"){
        return;

    }

//    send_email_by_cron();
//    die();
//    getAssignmentName($assignId);
//    overrideAssignEmailStudent($emailofUser, $courseID,$courseName, $component, $assignmentName,$assignmentDate,$assignmentOverrideDate,$assignment_url);

}

function updateData($event){
    global $COURSE;
    $courseObject = $COURSE;
    $event_data = $event->get_data();
    $userid = $event_data["relateduserid"];
    $courseid = $event_data["courseid"];
    $contextinstanceid = $event_data["contextinstanceid"];
    $assignid = $event_data['other']["assignid"];


    $record = getAssignOverride($userid,$assignid);
    $newDueDate = $record->duedate;
    $newAllowSubmissionFromDate = $record->allowsubmissionsfromdate;
    $newCutOffDate = $record->cutoffdate;

//    var_dump($record);
//    die();
    updateReminderEmailTable($courseid,$userid,$assignid,$newCutOffDate,$newDueDate,$contextinstanceid);

}

function deleteData($event){
    global $COURSE;
    $courseObject = $COURSE;
    $event_data = $event->get_data();
//    var_dump($event_data);
    $courseid = $event_data["courseid"];
    $studentid = $event_data["relateduserid"];
    $contextinstanceid = $event_data["contextinstanceid"];
    $table_id = get_id_reminder_email_table($courseid,$studentid,$contextinstanceid);

    deleteReminderEmailTable($table_id);


//    die();
}

function get_id_reminder_email_table($courseid,$studentid,$contextinstanceid){
    global $DB;
    $table = "local_course_reminder_email";
    $get_id = $DB->get_record($table,["courseid" => $courseid, "studentid"=>$studentid, "contextinstanceid"=>$contextinstanceid],"id");
    $get_id = $get_id->id;
    return $get_id;
}

function deleteReminderEmailTable($id){
    global $DB;
    $table = "local_course_reminder_email";
    $deleteRecord = $DB->delete_records($table,["id"=>$id]);

}

function getAssignOverride($userid, $assignid){
    global $DB;
    $record = $DB->get_record("assign_overrides",array('userid'=>$userid,'assignid'=>$assignid),'allowsubmissionsfromdate,duedate,cutoffdate,id');
//    var_dump($record);
//    die();
    return $record;
}
function updateReminderEmailTable($courseid, $studentid, $assignid,$newCutOffDate,$newDueDate, $contextinstanceid){
    global $DB;
    $table = 'local_course_reminder_email';
    $record = $DB->get_record('local_course_reminder_email',array('courseid'=>$courseid, 'studentid'=>$studentid,'assignmentid'=>$assignid, 'contextinstanceid'=>$contextinstanceid),'*' );



    $object = new \stdClass();

    $object->id = $record->id;
    $object->assignmentoverridedate = $newDueDate;
    $object->emailtosent = "1";
    $object->emailsent = "0";

    $updateObj = $DB->update_record($table,$object);
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
    public static function assign_user_override_updated(\mod_assign\event\user_override_updated $event){

        return updateData($event);

    }
    public static function assign_user_override_deleted(\mod_assign\event\user_override_deleted $event){

        return deleteData($event);
    }

    public static function quiz_user_override_created(\mod_quiz\event\user_override_created $event)
    {
        return getData($event);
    }
    public static function quiz_user_override_updated()
    {

    }


}





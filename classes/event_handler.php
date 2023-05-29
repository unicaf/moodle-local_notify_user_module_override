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

//require __DIR__.'/emails.php';

include_once($CFG->dirroot."/local/course_reminder/classes/emails.php");
//require_once($CFG->dirroot ."/config.php");
require __DIR__.'/checkStatus.php';
require_once($CFG->dirroot.'/group/lib.php');


use core\event\assessable_submitted;
use core_analytics\course;
use Matrix\Exception;
use \mod_assign\event\user_override_created;

function getData($event)
{


    // Defines $COURSE object
    global $COURSE;
    $courseObject = $COURSE;
    //GETS DATA FROM THE COURSE
    $event_data = $event->get_data();
    //COURSE ID
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
    $emailofUser = \core_user::get_user($relatedStudent);

    //Course ID
//    $courseID = $event_data["courseid"];


    // Course NAME
    $courseName = $courseObject->fullname;

    // Either mod_assign or mod_quiz
    $component = $event_data["component"];
    // This is passed to create the correct URL for the email
    $contextinstanceid = $event_data["contextinstanceid"];




    //FOR ASSIGNMENT
    if ($component === "mod_assign") {
        $component = "assignment";
        $assignId = $event_data["other"]["assignid"];
        $assignment_url = get_assignment_url($contextinstanceid, $component);
        $assignmentName = getAssignmentName($assignId, $table = "assign");
        //Assignment Date
        $assignmentDate = getAssignmentDate($assignId, $table = "assign", $component);

        //Assignment Override Date
        $assignmentOverrideDate = getAssignmentOverrideDate($assignId, $table = "assign_overrides", $relatedStudent, $component);
//      var_dump("Orignal date is ".$assignmentDate->duedate . " the new date override is " . $assignmentOverrideDate->duedate);
//        var_dump(get_teacher());
//        die();
        $assignmentDate = $assignmentDate->duedate;


        $assignmentOverrideDate = $assignmentOverrideDate->duedate;

    //FOR QUIZ
    } elseif ($component == "mod_quiz") {
        $component = "quiz";
        $assignId = $event_data["other"]["quizid"];
        $assignment_url = get_assignment_url($contextinstanceid, $component);
        $assignmentName = getAssignmentName($assignId, $table = "quiz");
        $assignmentName = $assignmentName->name;

        //Quiz Date
        $assignmentDate = getAssignmentDate($assignId, $table = "quiz", $component);
        $assignmentDate = $assignmentDate->timeclose;

        //Assignment Override Date
        $assignmentOverrideDate = getAssignmentOverrideDate($assignId, $table = "quiz_overrides", $relatedStudent, $component);
        $assignmentOverrideDate = $assignmentOverrideDate->timeclose;


    } else {
        //create error message
        $component = "assignment / quiz";
    }


    $who_to_send->who_to_send_notification($emailofUser, $courseName, $component, $assignmentName, $assignId, $assignmentDate, $assignmentOverrideDate, $assignment_url, $contextinstanceid);

// This checks to proceed with the script if the enable field in local_course_reminder table is set to 1 (enabled) SO IT DOESNT RUN IF ITS OFF IN SETTINGS
    if (!$is_enabled == "1") {
        return;

    }


}

function updateData($event)
{
    global $COURSE;
    $courseObject = $COURSE;
    //GETS COURSE DATA
    $event_data = $event->get_data();
//    var_dump($event_data);
//    die();

    $component = $event_data["component"];

    $userid = $event_data["relateduserid"];
    $courseid = $event_data["courseid"];
    $contextinstanceid = $event_data["contextinstanceid"];

    //FOR QUIZ
    if ($component == "mod_quiz") {
        $component = "quiz";
        $assignid = $event_data['other']['quizid'];


        //FOR ASSIGNMENT
    } elseif ($component == 'mod_assign') {
        $component = 'assignment';
        $assignid = $event_data['other']["assignid"];
    }

    //GETS ASSIGNMENT/QUIZ OVERRIDE DATE
    $record = getAssignOverride($userid, $assignid, $component);

    if ($component == 'quiz') {
        $newDueDate = $record->timeclose;
        $newAllowSubmissionFromDate = $record->timeopen;
        $newCutOffDate = null;
    } elseif ($component == 'assignment') {
        $newDueDate = $record->duedate;
        $newAllowSubmissionFromDate = $record->allowsubmissionsfromdate;
        $newCutOffDate = $record->cutoffdate;
    }

    //UPDATES TABLE
    updateReminderEmailTable($courseid, $userid, $assignid, $newCutOffDate, $newDueDate, $contextinstanceid, $component);

}

function get_original_date($assignid, $component)
{
    //Gets original date of assignment/quiz
    global $DB;
    if ($component === "quiz") {
        $table = 'quiz';
        return $DB->get_record($table, array('id' => $assignid), 'timeclose');
    } elseif ($component === 'assignment') {
        $table = "assign";
        return $DB->get_record($table, array('id' => $assignid), 'duedate');
    }
}

function deleteData($event)
{
    //USED IN DELETE AN OVERRIDE
    global $COURSE;
    $courseObject = $COURSE;
    //GETS COURSE DATA
    $event_data = $event->get_data();
//    var_dump($event_data);
    $courseid = $event_data["courseid"];
    $studentid = $event_data["relateduserid"];
    $contextinstanceid = $event_data["contextinstanceid"];

    //GETS THE ID FOR THE ASSIGNMENT/QUIZ IF WE HAVE ALREADY DELETED MANUALLY FROM DATABASE , WE RETURN SO IT DOESNT PRODUCE AN ERROR
    $table_id = get_id_reminder_email_table($courseid, $studentid, $contextinstanceid);
    //DELETES THE FIELD
    deleteReminderEmailTable($table_id);


}

//Gets ID from local_course_reminder_email
function get_id_reminder_email_table($courseid, $studentid, $contextinstanceid)
{
    global $DB;
    $table = "local_course_reminder_email";
    $get_id = $DB->get_record($table, ["courseid" => $courseid, "studentid" => $studentid, "contextinstanceid" => $contextinstanceid], "id");
    //If there is no ID in table due of reset or upgrade return to not show error.
    if (!$get_id) {
        //IF THERE IS NO ID IT RETURNS SO IT DOESNT PRODUCE AN ERROR
        return;
    }
    return $get_id->id;
}

//Deletes record in local_course_reminder_email
function deleteReminderEmailTable($id)
{
    //DELETES RECORD OF OVERRIDE
    global $DB;
    $table = "local_course_reminder_email";
    $deleteRecord = $DB->delete_records($table, ["id" => $id]);

}

function getAssignOverride($userid, $assignid, $component)
{
    //RETURNS DIFFRENT FIELDS FOR ASSIGNMENT/QUIZ AND GETS THE OVERRIDE DETAILS
    global $DB;
    if ($component == 'quiz') {
        $table = "quiz_overrides";
        $assignmentOrQuiz = "quiz";
        $fields = 'timeopen,timeclose,id';
    } elseif ($component == "assignment") {
        $table = "assign_overrides";
        $assignmentOrQuiz = "assignid";
        $fields = 'allowsubmissionsfromdate,duedate,cutoffdate,id';

    }

    $record = $DB->get_record($table, array('userid' => $userid, $assignmentOrQuiz => $assignid), $fields);
//    var_dump($record);
//    die();
    return $record;
}

function updateReminderEmailTable($courseid, $studentid, $assignid, $newCutOffDate, $newDueDate, $contextinstanceid, $component)
{
    //USED TO UPDATE THE RECORD
    global $DB;
    $table = 'local_course_reminder_email';


    if ($component == "quiz") {
        $quizid_or_assignmentid = 'quizid';
    } elseif ($component == 'assignment') {
        $quizid_or_assignmentid = 'assignmentid';
    }

    $record = $DB->get_record('local_course_reminder_email', array('courseid' => $courseid, 'studentid' => $studentid, $quizid_or_assignmentid => $assignid, 'contextinstanceid' => $contextinstanceid), '*');
    if (!$record) {
        //This is in place to fix errors when there is no record in our table but there is an override already set
        $main_date = get_original_date($assignid, $component);
//        var_dump($main_date);
        //INSERTS INTO TABLE IF NOT FOUND
        insert_course_reminder_email_table($courseid, $studentid, $assignid, $component, $main_date, $newDueDate, $contextinstanceid);
        return;
    }


    $object = new \stdClass();

    $object->id = $record->id;
    $object->assignmentoverridedate = $newDueDate;
    $object->emailtosent = sync_to_send_email($courseid)->enable;
    $object->emailsent = "0";


    $updateObj = $DB->update_record($table, $object);
}

//Gets the name of the assignment/quiz
function getAssignmentName($id, $table)
{
    /* In this function we require two parameters , $id and $table. The ID is the ID of the assignment or quiz
    and the table is for the database table (mdl_assign is for assignments and mdl_quiz for quizes)
    We then create a database connection and add our $table parameter passed from getData and return back
    an object with the name of the assignment/quiz
    */
    global $DB;
    $assignmentName = $DB->get_record("$table", array('id' => $id), 'name');
//    var_dump($assignmentName);
    return $assignmentName;
}

function getAssignmentDate($id, $table, $component)
{
    //GETS ORIGINAL DUE DATE
    global $DB;

    if ($component == "assignment" && $table == "assign") {
        return $DB->get_record($table, array('id' => $id), 'duedate');
    } elseif ($component == "quiz" && $table == "quiz") {
        return $DB->get_record($table, array('id' => $id), 'timeclose');
    }


}

function getAssignmentOverrideDate($id, $table, $relatedStudent, $component)
{
    //GETS OVERRIDE DATE
    global $DB;
    if ($component == "assignment" && $table == "assign_overrides") {

        return $DB->get_record($table, array('assignid' => $id, "userid" => $relatedStudent), 'duedate');
    } elseif ($component == "quiz" && $table == "quiz_overrides") {
        return $DB->get_record($table, array('quiz' => $id, 'userid' => $relatedStudent), 'timeclose');
    }


}

function insert_course_reminder_email_table($courseid, $studentid, $quiz_or_assignment_ID, $component, $assignmentdate, $assignmentoverridedate, $contextinstanceid)
{
    //Adds record into database function
    global $DB;
    $table = 'local_course_reminder_email';
    $record = new \stdClass();
    $record->courseid = $courseid;
    $record->studentid = $studentid;
    $record->component = $component;
    $record->assignmentdate = $assignmentdate;
    $record->assignmentoverridedate = $assignmentoverridedate;
    $record->contextinstanceid = $contextinstanceid;
    $record->emailtosent = sync_to_send_email($courseid)->enable;
    $record->emailsent = '0';
    if ($component === 'quiz' || $component === "mod_quiz") {
        $record->quizid = $quiz_or_assignment_ID;
        $record->assignmentdate = $assignmentdate->timeclose;
    } elseif ($component === 'assignment' || $component === 'assign') {
        $record->assignmentid = $quiz_or_assignment_ID;
        $record->assignmentdate = $assignmentdate->duedate;
    }
    return $DB->insert_record($table, $record);


}

//Creates URL link for assignment
function get_assignment_url($contextinstanceid, $component)
{
    //CREATES URL FOR ASSIGNMENT/QUIZ
    if ($component == "mod_assign") {
        return new \moodle_url('/mod/assign/view.php', array('id' => $contextinstanceid));
    } elseif ($component == "mod_quiz") {
        return new \moodle_url('/mod/quiz/view.php', array('id' => $contextinstanceid));
    }
}


function get_student_group($courseid, $userid)
{
    $group = groups_get_user_groups($courseid, $userid);
}


function sync_to_send_email($courseid)
{

    global $DB;
    $is_enabled = $DB->get_record('local_course_reminder', ['courseid' => $courseid], 'enable');
    //checks if record is in table local_course_reminder if not it adds it
    if (!$is_enabled) {
        $test = new \checkStatusClass($courseid);
        $test->checkStatus();
        return $is_enabled;
    }
    return $is_enabled;


}


function copy_course($event){

  $origin_backup_course_id = $event->objectid;
}

class event_handler
{

    public static function assign_user_override_created(\mod_assign\event\user_override_created $event)
    {
        //ASSIGNMENT CREATE AN OVERRIDE

        return getData($event);
    }

    public static function assign_user_override_updated(\mod_assign\event\user_override_updated $event)
    {
        //ASSIGNMENT UPDATE AN OVERRIDE
        return updateData($event);

    }

    public static function assign_user_override_deleted(\mod_assign\event\user_override_deleted $event)
    {
        //ASSIGNMENT DELETE AN OVERRIDE

        return deleteData($event);
    }

    public static function quiz_user_override_created(\mod_quiz\event\user_override_created $event)
    {
        //QUIZ ADD A NEW OVERRIDE
        return getData($event);
    }

    public static function quiz_user_override_updated(\mod_quiz\event\user_override_updated $event)
    {
        //QUIZ UPDATE AN OVERRIDE
        return updateData($event);
    }

    public static function quiz_user_override_deleted(\mod_quiz\event\user_override_deleted $event)
    {
        //QUIZ DELETE AN OVERRIDE

        return deleteData($event);
    }

    public static function copy_course(\core\event\course_backup_created $event)
    {
        return copy_course($event);
    }


}





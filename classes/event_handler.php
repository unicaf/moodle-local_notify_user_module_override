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
function getStudentEmail($event)
{
    global $COURSE;

    $courseObject = $COURSE;
    $event_data = $event->get_data();

    //related user is the user which is affected - student
    $relatedStudent = $event_data["relateduserid"];
    $emailofUser= \core_user::get_user($relatedStudent);
    //Get Teacher who gave the extension
    $relatedTeacher = $event_data["userid"];
    $emailofTeacher = \core_user::get_user($relatedTeacher);
    $courseID = $event_data["courseid"];
    $courseName = $courseObject->fullname;
    $component = $event_data["component"];
    $assignId;


    if($component === "mod_assign"){
        $assignId = $event_data["other"]["assignid"];
        $assignmentName = getAssignmentName($assignId, $table="assign");
        $component = "Assignment";
    }elseif($component=="mod_quiz"){
        $assignId = $event_data["other"]["quizid"];
        $assignmentName = getAssignmentName($assignId, $table="quiz");
        $component = "Quiz";
    }else{
        //create error message
        $component = "Assignment / Quiz";
    }

//    getAssignmentName($assignId);
    overrideAssignEmailStudent($emailofUser, $emailofTeacher, $courseID,$courseName, $component, $assignmentName);

}

function getAssignmentName($id,$table){
    /* In this function we require two parameters , $id and $table. The ID is the ID of the assignment or quiz
    and the table is for the database table (mdl_assign is for assignments and mdl_quiz for quizes)
    We then create a database connection and add our $table parameter passed from getStudentEmail and return back
    an object with the name of the assignment/quiz


  */
    global $DB;
    $assignmentName = $DB->get_record("$table",array('id'=>$id),'name');
//    var_dump($assignmentName);
    return $assignmentName;
}




class event_handler
{

    public static function assign_user_override_created(\mod_assign\event\user_override_created $event)
    {


        return getStudentEmail($event);
    }
    public static function assign_user_override_updated(){

    }

    public static function quiz_user_override_created(\mod_quiz\event\user_override_created $event)
    {
        return getStudentEmail($event);
    }
    public static function quiz_user_override_updated()
    {

    }


}





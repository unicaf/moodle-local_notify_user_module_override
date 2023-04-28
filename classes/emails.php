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



function overrideAssignEmailStudent($emailofUser, $courseid,$courseName, $component, $assignmentName,$assignmentDate,$assignmentOverrideDate,$assignment_url){
    // Send email to user
    $assignmentName = $assignmentName->name;
    //Creates the url for moodle
    $assignment_url = html_writer::link($assignment_url,$assignmentName);

    //Email of Unicaf extenuating Circumstances
    $extenuatingCircumstances = html_writer::link("extenuating.circumstances@unicaf.org","extenuating.circumstances@unicaf.org");
    //Email no-reply
    $emailFrom =core_user::get_noreply_user();
    // Email of the student
    $emailToUser = $emailofUser;
    //Subject of email
    $subject = "Your course " .$courseName ." has some changes in ".$component .  " has changed dates";
    //Message of email
    $message = "Dear ".$emailofUser->firstname . "\n\n Following the review of your extenuating circumstances claim, we would like to inform you that your application for an extenstion for  " .$component ." ".$assignment_url  ." 
    has been aprroved .\n\n The assessment deadline for ". $assignment_url ." has been changed from ".$assignmentDate . " to  <strong> ".$assignmentOverrideDate ." </strong>. \n\n"
    ."In case you have already submitted " .$component ." ".$assignment_url ." prior or on " . $assignmentOverrideDate .", then rest assured that your assignment will be sent for marking .\n\n
     In case you are yet to submit " .$component ." " . "$assignment_url" . ", please do so prior to the new extended deadline " . $assignmentOverrideDate .
     "\n\n Should you require any further clarification, please do not hesitate to contact the Unicaf Extenuating Circumstances team directly on ".$extenuatingCircumstances;
    // Function to send email
    email_to_user($emailToUser,$emailFrom,$subject,$message,nl2br($message),"","","");


}


//function send_email_by_cron(){
//    global $DB;
//    $table= 'local_course_reminder_email';
//    $get_record_for_cron = $DB->get_records($table,["emailtosent"=>"1","emailsent"=>"0"],'',"*");
////    print_r($get_record_for_cron);
//
//    for($i=0; $i < count($get_record_for_cron); $i++){
//        $keys =array_keys($get_record_for_cron);
////        echo $keys[$i] . "{<br>";
//        foreach($get_record_for_cron[$keys[$i]] as $key => $value){
////            echo $key . " : " . $value ."<br>";
//            $courseid = array_column($get_record_for_cron,"courseid","id");
//            $id = array_column($get_record_for_cron,"id","id");
//            $studentid = array_column($get_record_for_cron, 'studentid', 'id');
//            $assignmentid = array_column($get_record_for_cron, 'assignmentid', 'id');
//            $component = array_column($get_record_for_cron,'component','id');
//            $assignmentdate = array_column($get_record_for_cron,'assignmentdate','id');
//            $assignmentoverridedate = array_column($get_record_for_cron,'assignmentoverridedate','id');
//
//
//
//
////            var_dump($id[$value]);
////            die();
////            email_Student($id[$value],$studentid[$value],$component[$value],$courseid[$value],$assignmentid[$value],$assignmentdate[$value],$assignmentoverridedate[$value]);
//            var_dump(email_sent($table,$id[$value]));
//
//
////
//
//        }
//
//
//    }
//
//}
function send_email_by_cron()
{
    global $DB;
    $table = 'local_course_reminder_email';
    $get_record_for_cron = $DB->get_records($table, ["emailtosent" => "1", "emailsent" => "0"], '', "*");
    $keys = array_keys($get_record_for_cron);
//    print_r($get_record_for_cron[66]->id);
//    print_r($keys);
//    print_r($get_record_for_cron);
    $idsArr = [];
    $courseIdArr = [];

    $object = new stdClass();
        for($i=0; $i<count($get_record_for_cron); $i++){
        foreach($get_record_for_cron[$keys[$i]] as $key => $value){
            $object->$key = $value;
            }
        email_Student($object);
        }






//    for($i=0; $i<count($get_record_for_cron); $i++){
//        foreach($get_record_for_cron[$keys[$i]] as $key => $value){
////            echo $key . " : " . $value ."<br>"  . "<br>";
////            print_r($key[$value]);
////            echo "<br>". $key . "<br>";
//            if($key == "id"){
//                $singleID =  $value;
//                $idsArr[] = $singleID;
////                var_dump($idsArr) ;
//            }
//            if($key =="courseid"){
//                $courseid = $value;
//                $courseIdArr[] = $courseid;
//                foreach($courseIdArr as $sample){
//                    print_r($sample);
//                }
////                var_dump($courseIdArr);
//
//            }
//
//
////            var_dump($ids);
//
//
//        }
//
//
//    }






//    var_dump(email_sent($table, $id[$value]));
}

function email_sent($table, $id){
    global $DB;

//    var_dump($id);
    $object = new stdClass();
    $object->id = $id;
    $object->emailsent = "1";
    $object->emailtosent = "0";
    $object->emailtime = sent_email_time();
//    var_dump($object);

    $DB->update_record($table,$object);




}
function sent_email_time(){
    return time();
}

function email_Student($studentObj){
    global $USER;
        print_r($studentObj);
    // Send email to user
    $assignmentName = $studentObj->assignmentid;
    //Creates the url for moodle
//    $assignment_url = html_writer::link($assignment_url,$assignmentName);

    //Email of Unicaf extenuating Circumstances
    $extenuatingCircumstances = html_writer::link("extenuating.circumstances@unicaf.org","extenuating.circumstances@unicaf.org");
    //Email no-reply
    $emailFrom =core_user::get_noreply_user();
    // Email of the student
    $student = $studentObj->studentid;
    $emailofStudent= \core_user::get_user($student);
//    print_r($emailofStudent);

    //Subject of email
    $subject = "Your course " .$studentObj->courseid ." has some changes in ".$studentObj->component .  " has changed dates";
    //Message of email
//    $message = "Dear ".$emailofUser->firstname . "\n\n Following the review of your extenuating circumstances claim, we would like to inform you that your application for an extenstion for  " .$component ." ".$assignment_url  ."
//        has been aprroved .\n\n The assessment deadline for ". $assignment_url ." has been changed from ".$assignmentDate . " to  <strong> ".$assignmentOverrideDate ." </strong>. \n\n"
//        ."In case you have already submitted " .$component ." ".$assignment_url ." prior or on " . $assignmentOverrideDate .", then rest assured that your assignment will be sent for marking .\n\n
//        In case you are yet to submit " .$component ." " . "$assignment_url" . ", please do so prior to the new extended deadline " . $assignmentOverrideDate .
//        "\n\n Should you require any further clarification, please do not hesitate to contact the Unicaf Extenuating Circumstances team directly on ".$extenuatingCircumstances;
    // Function to send email
    $message = "This is a test that it works";
    email_to_user($emailofStudent,$emailFrom,$subject,$message,nl2br($message),"","","");
    email_sent("local_course_reminder_email",$studentObj->id);


}

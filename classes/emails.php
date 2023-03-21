<?php

print_r("Emails page");
function overrideAssignEmailStudent($emailofUser, $emailofTeacher, $courseid,$assignID, $component){
    // Send email to user
//    print_r($emailofUser);
//    print_r($emailofTeacher);
//    print_r($courseid);
//    print_r($assignID);
//    die();
    if($component === "mod_assign"){
        $component = "Assignment";
    }elseif($component=="mod_quiz"){
        $component = "Quiz";
    }else{
        $component = "Assignment / Quiz";
    }
    $emailFrom =core_user::get_noreply_user();
    $emailToUser = $emailofUser;
    $subject = "Your course with ID ".$courseid . " with assignment ID ".$assignID . " has changed dates";
    $message = "Dear ".$emailofUser->firstname . " Your " .$component . " with ID ".$assignID . " has changed";
    email_to_user($emailToUser,$emailFrom,$subject,$message,$message,"","","");


}

//function overrideAssignEmailTeacher()
//{
//    // Send email to Teacher
//    $emailFrom = core_user::get_noreply_user();
//    $emailToUser = $emailofUser;
//    $subject = "User Extensions";
//    $message = "This works with a user extension";
//    email_to_user($emailToUser, $emailFrom, $subject, $message, $message, "", "", "");
//}
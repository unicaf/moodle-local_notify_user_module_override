<?php

print_r("Emails page");
function overrideAssignEmailStudent($emailofUser, $emailofTeacher, $courseid, $component){
    // Send email to user
    if($component === "mod_assign"){
        $component = "Assignment";
    }elseif($component=="mod_quiz"){
        $component = "Quiz";
    }else{
        $component = "Assignment / Quiz";
    }
    $emailFrom =core_user::get_noreply_user();
    $emailToUser = $emailofUser;
    $subject = "Your course with ID ".$courseid . " with assignment ID " . " has changed dates";
    $message = "Dear ".$emailofUser->firstname . " Your " .$component .  " has changed";
    email_to_user($emailToUser,$emailFrom,$subject,$message,$message,"","","");


}

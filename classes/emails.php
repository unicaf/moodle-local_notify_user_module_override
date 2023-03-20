<?php

print_r("Emails page");
function overrideEmailUser($relateduserid){
    // Send email to user
    $emailFrom =core_user::get_noreply_user();
    $emailToUser = $relateduserid;
    $subject = "User Extensions";
    $message = "This works with a user extension";
    email_to_user($emailToUser,$emailFrom,$subject,$message,$message,"","","");

}
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
    //Email no-reply
    $emailFrom =core_user::get_noreply_user();
    // Email of the student
    $emailToUser = $emailofUser;
    //Subject of email
    $subject = "Your course " .$courseName ." has some changes in ".$component .  " has changed dates";
    //Message of email
    $message = "Dear ".$emailofUser->firstname . " your " .$component ." ". $assignment_url . " has changed Your date changed from " .$assignmentDate . " and your new date is ".$assignmentOverrideDate ." .";
    // Function to send email
    email_to_user($emailToUser,$emailFrom,$subject,$message,$message,"","","");


}

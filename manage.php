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

require_once(__DIR__ . '/../../config.php');
//require_once($CFG->dirroot.'/blocks/course_overview/locallib.php');

global $DB,$USER;

//$event_data = $event->get_data();
//var_dump($event_data);
//die;
//$record = $DB->get_record($event_data['objecttable'], ['id' => $event_data['objectid']], '*');


$PAGE->set_url(new moodle_url('/local/course_reminder/manage.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title('List Course deadlines');


echo $OUTPUT->header();

//function getmails(){
//    $emailFrom =core_user::get_noreply_user();
//
//    $admins = get_admin();
//    print_r($admins->email);
////    print_r($emailFrom->email);
//    $subject = "User Extensions";
//    $message = "This works with a user extension";
//    email_to_user($admins,$emailFrom,$subject,$message,$message,"","","");
//}
//getmails();
//print_r(__DIR__);


echo $OUTPUT->footer();



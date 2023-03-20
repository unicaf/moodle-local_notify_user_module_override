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


use core\event\assessable_submitted;
use \mod_assign\event\user_override_created;

class event_handler
{

    public static function assign_user_override_created(\mod_assign\event\user_override_created $event)
    {
//    getmails($event);
        $event_data = $event->get_data();
//        var_dump(json_encode($event_data));
        $userid = $event_data["userid"];
        print_r($userid);
        die();
    }
    public static function assign_user_override_updated(){

    }

    public static function quiz_user_override_created()
    {

    }
    public static function quiz_user_override_updated()
    {

    }


}


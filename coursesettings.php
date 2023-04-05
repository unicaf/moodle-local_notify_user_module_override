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



require_once('../../config.php');
global $DB, $COURSE;
//$course = $DB->get_record('course', array('id' => "$courseid"));

$PAGE->set_url(new moodle_url('/local/course_reminder/coursesettings.php', array('courseid' =>$PAGE->course->id)));
$courseid = optional_param("courseid",null,PARAM_INT);
$PAGE->set_context(context_course::instance($courseid));
$PAGE->set_title("Customize Reminder Settings");
$PAGE->set_heading(get_string('pluginname','local_course_reminder'));



echo $OUTPUT->header();

print_r("Hello");
echo $OUTPUT->footer();
die();
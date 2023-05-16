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

function xmldb_local_course_reminder_upgrade($oldversion) {
    global $CFG, $DB;
    $dbman = $DB->get_manager();

    $result = TRUE;
    if ($oldversion < 2014121301) {

        // Define table local_course_reminder to be created.
        $table = new xmldb_table('local_course_reminder');

        // Adding fields to table local_course_reminder.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $table->add_field('enable', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1');

        // Adding keys to table local_course_reminder.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('relatedcourse', XMLDB_KEY_UNIQUE, ['courseid']);

        // Conditionally launch create table for local_course_reminder.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Course_reminder savepoint reached.
        upgrade_plugin_savepoint(true, $oldversion, 'local', 'course_reminder');
    }
    if ($oldversion < 2014121301) {

        // Define table local_course_reminder_email to be created.
        $table = new xmldb_table('local_course_reminder_email');

        // Adding fields to table local_course_reminder_email.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('studentid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('emailtosent', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('emailsent', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('assignmentid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('quizid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table local_course_reminder_email.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('studentid', XMLDB_KEY_FOREIGN, ['studentid'], 'user', ['id']);
        $table->add_key('courseid', XMLDB_KEY_FOREIGN, ['courseid'], 'course', ['id']);
        $table->add_key('assignmentid', XMLDB_KEY_FOREIGN, ['assignmentid'], 'assign', ['id']);
        $table->add_key('quizid', XMLDB_KEY_FOREIGN, ['quizid'], 'quiz', ['id']);

        // Conditionally launch create table for local_course_reminder_email.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Course_reminder savepoint reached.
        upgrade_plugin_savepoint(true, $oldversion, 'local', 'course_reminder');
    }
    return $result;

}



?>
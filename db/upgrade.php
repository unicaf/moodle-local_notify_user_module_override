<?php

function xmldb_local_course_reminder_upgrade($oldversion) {
    global $CFG, $DB;
    $dbman = $DB->get_manager();

    $result = TRUE;

    if ($oldversion < XXXXXXXXXX) {

        // Define table local_course_reminder to be created.
        $table = new xmldb_table('local_course_reminder');

        // Adding fields to table local_course_reminder.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);

        // Adding keys to table local_course_reminder.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for local_course_reminder.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Course_reminder savepoint reached.
        upgrade_plugin_savepoint(true, XXXXXXXXXX, 'local', 'course_reminder');
    }


    return $result;
}
?>
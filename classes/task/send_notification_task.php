<?php

namespace local_course_reminder\task;
require_once($CFG->dirroot ."/local/course_reminder/classes/emails.php");
class send_notification_task extends \core\task\scheduled_task
{
    public function get_name()
    {
        // TODO: Implement get_name() method.
        return get_string('pluginfullname', 'local_course_reminder');
    }

    public function execute()
    {
       send_email_by_cron();


    }

}
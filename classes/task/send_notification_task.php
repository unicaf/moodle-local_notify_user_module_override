<?php

namespace local_course_reminder\task;
class send_notification_task extends \core\task\scheduled_task
{
    public function get_name()
    {
        // TODO: Implement get_name() method.
        return get_string('pluginfullname', 'local_course_reminder');
    }

    public function execute()
    {
        mtrace("My task started");


        mtrace("My task finished");


    }

}
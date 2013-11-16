<?php
class AddExterneDatenMigration extends DBMigration
{
    function up() 
    {
        $new_job = array(
            'filename'    => 'public/plugins_packages/THW/DigitaleLagekarte/fetch_external_data.class.php',
            'class'       => 'FetchExternalDataJob',
            'priority'    => 'normal',
            'minute'      => '-1'
        );

        $query = "INSERT IGNORE INTO `cronjobs_tasks`
                    (`task_id`, `filename`, `class`, `active`)
                  VALUES (:task_id, :filename, :class, 1)";
        $task_statement = DBManager::get()->prepare($query);

        $query = "INSERT IGNORE INTO `cronjobs_schedules`
                    (`schedule_id`, `task_id`, `parameters`, `priority`,
                     `type`, `minute`, `mkdate`, `chdate`,
                     `last_result`)
                  VALUES (:schedule_id, :task_id, '[]', :priority, 'periodic',
                          :minute, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(),
                          NULL)";
        $schedule_statement = DBManager::get()->prepare($query);


        $task_id = md5(uniqid('task', true));

        $task_statement->execute(array(
            ':task_id'  => $task_id,
            ':filename' => $new_job['filename'],
            ':class'    => $new_job['class'],
        ));

        $schedule_id = md5(uniqid('schedule', true));
        $schedule_statement->execute(array(
            ':schedule_id' => $schedule_id,
            ':task_id'     => $task_id,
            ':priority'    => $new_job['priority'],
            ':minute'      => $new_job['minute'],
        ));
        
        //Neue Tabelle:
        $create_table = DBManager::get()->prepare("
            CREATE TABLE IF NOT EXISTS `katip_external_data_urls` (
                `Seminar_id` varchar(32) NOT NULL,
                `url` varchar(100) NOT NULL,
                `name` varchar(100) NOT NULL,
                `active` tinyint(4) NOT NULL DEFAULT '0',
                `last_object` longtext,
                `last_update` bigint(20) DEFAULT NULL,
                `auth_user` int(11) DEFAULT NULL,
                `auth_pw` int(11) DEFAULT NULL,
                `mapping` text,
                `chdate` int(11) NOT NULL,
                `mkdate` int(11) NOT NULL,
                PRIMARY KEY (`Seminar_id`,`url`),
                KEY `Seminar_id` (`Seminar_id`)
            ) ENGINE=MyISAM
        ");
        $create_table->execute();
    }
}
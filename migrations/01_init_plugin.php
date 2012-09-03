<?php
class InitPlugin extends DBMigration
{
    function up() 
    {
        DBManager::get()->exec("
            CREATE TABLE IF NOT EXISTS `katip_lagekarte` (
            `seminar_id` varchar(32) NOT NULL,
            `longitude` double NOT NULL,
            `latitude` double NOT NULL,
            `zoom` int(11) NOT NULL,
            `chdate` bigint(20) NOT NULL,
            PRIMARY KEY (`seminar_id`)
            ) ENGINE=MyISAM
        ");
    }
}
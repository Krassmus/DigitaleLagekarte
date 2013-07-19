<?php
class InitPlugin extends DBMigration
{
    function up() 
    {
        DBManager::get()->exec("
            CREATE TABLE IF NOT EXISTS `katip_lagekarte` (
            `map_id` varchar(32) NOT NULL,
            `seminar_id` varchar(32) NOT NULL,
            `latitude` double NOT NULL,
            `longitude` double NOT NULL,
            `zoom` int(11) NOT NULL,
            `user_id` varchar(32) NOT NULL,
            `chdate` bigint(20) NOT NULL,
            `mkdate` int(11) NOT NULL,
            PRIMARY KEY (`map_id`)
            ) ENGINE=MyISAM
        ");
    }
}
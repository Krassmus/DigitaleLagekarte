<?php
class CreateSchadenskonten extends DBMigration
{
    function up() 
    {
        DBManager::get()->exec("
            CREATE TABLE IF NOT EXISTS `katip_schadenskonten` (
            `schadenskonto_id` varchar(32) NOT NULL,
            `map_id` varchar(32) NOT NULL,
            `title` TEXT NOT NULL,
            `chdate` bigint(20) NOT NULL,
            `mkdate` int(11) NOT NULL,
            PRIMARY KEY (`schadenskonto_id`),
            KEY `map_id` (`map_id`)
            ) ENGINE=MyISAM
        ");
        DBManager::get()->exec("
            CREATE TABLE IF NOT EXISTS `katip_poi` (
            `poi_id` varchar(32) NOT NULL,
            `schadenskonto_id` varchar(32) NOT NULL,
            `shape` varchar(16),
            `title` TEXT NULL,
            `image` varchar(128) NULL,
            `coords` TEXT NOT NULL,
            `chdate` bigint(20) NOT NULL,
            `mkdate` int(11) NOT NULL,
            PRIMARY KEY (`poi_id`),
            KEY `schadenskonto_id` (`schadenskonto_id`)
            ) ENGINE=MyISAM
        ");
    }
}
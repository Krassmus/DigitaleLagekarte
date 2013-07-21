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
            `predecessor` varchar(32) NÚLL,
            `first_predecessor` varchar(32) NULL,
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
            `shape` varchar(16) NULL,
            `title` TEXT NULL,
            `image` varchar(128) NULL,
            `coordinates` TEXT NOT NULL,
            `radius` INTEGER NULL,
            `predecessor` varchar(32) NÚLL,
            `first_predecessor` varchar(32) NULL,
            `chdate` bigint(20) NOT NULL,
            `mkdate` int(11) NOT NULL,
            PRIMARY KEY (`poi_id`),
            KEY `schadenskonto_id` (`schadenskonto_id`)
            ) ENGINE=MyISAM
        ");
    }
}
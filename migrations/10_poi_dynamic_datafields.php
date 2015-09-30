<?php
class PoiDynamicDatafields extends DBMigration
{
    function up()
    {
        DBManager::get()->exec("
            CREATE TABLE IF NOT EXISTS `katip_poi_datafields` (
              `datafield_id` varchar(32) NOT NULL,
              `poi_id` varchar(32) NOT NULL,
              `name` text NOT NULL,
              `content` text NOT NULL,
              `chdate` bigint(20) NOT NULL,
              `mkdate` bigint(20) NOT NULL,
              PRIMARY KEY (`datafield_id`)
            ) ENGINE=MyISAM
        ");
    }
}
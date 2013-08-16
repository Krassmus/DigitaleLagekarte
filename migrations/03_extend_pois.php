<?php
class ExtendPois extends DBMigration
{
    function up() 
    {
        DBManager::get()->exec("
            ALTER TABLE `katip_poi` 
                ADD `color` VARCHAR( 30 ) NULL AFTER `radius` ,
                ADD `visible` TINYINT( 2 ) NOT NULL DEFAULT '1' AFTER `color` ;
        ");
        
    }
}
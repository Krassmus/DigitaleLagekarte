<?php
class RecursivePois extends Migration
{
    function up() 
    {
        DBManager::get()->exec("
            ALTER TABLE `katip_poi` ADD `parent_id` VARCHAR( 32 ) NULL AFTER `schadenskonto_id` ;
        ");
        
    }
}
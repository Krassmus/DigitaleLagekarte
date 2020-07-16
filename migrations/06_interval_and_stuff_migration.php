<?php
class IntervalAndStuffMigration extends Migration
{
    function up()
    {
        $create_interval = DBManager::get()->prepare("
            ALTER TABLE `katip_external_data_urls` ADD `interval` INT NOT NULL DEFAULT '15' AFTER `active` ;
        ");
        $create_interval->execute();
    }
}
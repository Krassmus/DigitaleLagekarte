<?php
class PoiSizeMigration extends DBMigration
{
    function up()
    {
        $alter_table = DBManager::get()->prepare("
            ALTER TABLE `katip_poi` ADD `size` INT NULL AFTER `color` ;
        ");
        $alter_table->execute();
    }
}
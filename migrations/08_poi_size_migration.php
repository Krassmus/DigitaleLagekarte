<?php
class PoiSizeMigration extends Migration
{
    function up()
    {
        $alter_table = DBManager::get()->prepare("
            ALTER TABLE `katip_poi` ADD `size` INT NULL AFTER `color` ;
        ");
        $alter_table->execute();
    }
}
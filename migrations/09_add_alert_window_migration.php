<?php
class AddAlertWindowMigration extends Migration
{
    function up()
    {
        $alter_table = DBManager::get()->prepare("
            ALTER TABLE `katip_lagekarte` ADD `alert_window_text` TEXT NULL AFTER `zoom` ;
        ");
        $alter_table->execute();
    }
}
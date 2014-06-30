<?php
class CustomTileServerMigration extends DBMigration
{
    function up()
    {
        $create_config = DBManager::get()->prepare("
            INSERT IGNORE INTO `config`
            SET `config_id` = MD5('KATIP_TILE_SERVER'),
                `parent_id` = '',
                `field` = 'KATIP_TILE_SERVER',
                `value` = 'http://{s}.tile.osm.org/{z}/{x}/{y}.png',
                `is_default` = '',
                `type` = 'string',
                `range` = 'global',
                `section` = 'plugins',
                `position` = 0,
                `mkdate` = UNIX_TIMESTAMP(),
                `chdate` = UNIX_TIMESTAMP(),
                `description` = 'Welcher Server soll für OSM-Tiles verwendet werden? Parameter: {s} means one of the available subdomains (used sequentially to help with browser parallel requests per domain limitation; subdomain values are specified in options; a, b or c by default, can be omitted), {z} — zoom level, {x} and {y} — tile coordinates.',
                `comment` = '',
                `message_template` = ''
        ");
        $create_config->execute();
    }
}
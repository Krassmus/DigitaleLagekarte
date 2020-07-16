<?php
class CustomTileServerMigration extends Migration
{
    function up()
    {
        Config::get()->create("KATIP_TILE_SERVER", array(
            'value' => 'http://{s}.tile.osm.org/{z}/{x}/{y}.png',
            'type' => "string",
            'section' => "THW",
            'description' => 'Welcher Server soll für OSM-Tiles verwendet werden? Parameter: {s} means one of the available subdomains (used sequentially to help with browser parallel requests per domain limitation; subdomain values are specified in options; a, b or c by default, can be omitted), {z} — zoom level, {x} and {y} — tile coordinates.'
        ));
    }
}
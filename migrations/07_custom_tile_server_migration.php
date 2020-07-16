<?php
class CustomTileServerMigration extends Migration
{
    function up()
    {
        Config::get()->create("KATIP_TILE_SERVER", array(
            'value' => 'http://{s}.tile.osm.org/{z}/{x}/{y}.png',
            'type' => "string",
            'section' => "THW",
            'description' => 'Server für OSM-Tiles. Parameter: {s} means one of the available subdomains, {z} — zoom level, {x} and {y} — tile coordinates.'
        ));
    }
}

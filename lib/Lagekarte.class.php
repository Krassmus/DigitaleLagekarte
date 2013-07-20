<?php
/*
 *  Copyright (c) 2012-2013  Rasmus Fuhse <fuhse@data-quest.de>
 *
 *  This program is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU General Public License as
 *  published by the Free Software Foundation; either version 2 of
 *  the License, or (at your option) any later version.
 */

class Lagekarte extends SimpleORMap {
    protected $db_table = "katip_lagekarte";
    
    static public function getCurrent($seminar_id) {
        $db = DBManager::get();
        $map_id = $db->query(
            "SELECT map_id " .
            "FROM `katip_lagekarte` " .
            "WHERE seminar_id = ".$db->quote($seminar_id)." " .
            "ORDER BY mkdate DESC " .
        "")->fetch(PDO::FETCH_COLUMN, 0);
        return new Lagekarte($map_id ? $map_id : null);
    }
    
    static public function copyFrom($old_map) {
        $map = new Lagekarte();
        $map['longitude'] = $old_map['longitude'];
        $map['latitude'] = $old_map['latitude'];
        $map['zoom'] = $old_map['zoom'];
        $map['seminar_id'] = $old_map['seminar_id'];
        return $map;
    }
}
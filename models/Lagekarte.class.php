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
        $map = new Lagekarte($map_id ? $map_id : null);
        if ($map->isNew()) {
            $map['seminar_id'] = $_SESSION['SessionSeminar'];
            $map['latitude'] = 53.152692;
            $map['longitude'] = 8.187937;
            $map['zoom'] = 17;
            $map['user_id'] = $GLOBALS['user_id'];
        }
        return $map;
    }
    
    public function createCopy() {
        $map = new Lagekarte();
        $map->setData($this->toArray());
        $map->setId($map->getNewId());
        $map['user_id'] = $GLOBALS['user']->id;
        $map->store();
        foreach ($this->getSchadenskonten() as $old_schadenskonto) {
            $new_schadenskonto = new Schadenskonto();
            $new_schadenskonto->setData($old_schadenskonto->toArray());
            $new_schadenskonto['map_id'] = $map->getId();
            $new_schadenskonto['predecessor'] = $old_schadenskonto->getId();
            $new_schadenskonto->setId($new_schadenskonto->getNewId());
            $new_schadenskonto->store();
            foreach ($old_schadenskonto->getPOIs() as $old_poi) {
                $new_poi = new PointOfInterest();
                $new_poi->setData($old_poi->toArray());
                $new_poi->setId($new_poi->getNewId());
                $new_poi['schadenskonto_id'] = $new_schadenskonto->getId();
                $new_poi['predecessor'] = $old_poi->getId();
                $new_poi->store();
            }
        }
        return $map;
    }
    
    public function getSchadenskonten() {
        return Schadenskonto::findBySQL("map_id = ? ORDER BY title ASC", array($this->getId()));
    }
}
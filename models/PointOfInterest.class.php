<?php
/*
 *  Copyright (c) 2012-2013  Rasmus Fuhse <fuhse@data-quest.de>
 *
 *  This program is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU General Public License as
 *  published by the Free Software Foundation; either version 2 of
 *  the License, or (at your option) any later version.
 */

class PointOfInterest extends SimpleORMap {
    protected $db_table = "katip_poi";
    
    static public function findCurrentByPoiID($poi_id) {
        $pois = self::findBySQL("first_predecessor = :poi_id OR poi_id = :poi_id ORDER BY mkdate DESC LIMIT 1", array('poi_id' => $poi_id));
        if (!count($pois)) {
            return false;
        }
        if ($pois[0]['first_predecessor'] === $poi_id) {
            return $pois[0];
        }
        $pois = self::findBySQL("first_predecessor = :poi_id ORDER BY mkdate DESC LIMIT 1", array('poi_id' => $pois[0]['first_predecessor']));
        return $pois[0];
    }
    
    static public function findCurrent($seminar_id) {
        $current_map = Lagekarte::getCurrent($seminar_id);
        $statement = DBManager::get()->prepare(
            "SELECT katip_poi.* " .
            "FROM katip_poi " .
                "INNER JOIN katip_schadenskonten ON (katip_schadenskonten.schadenskonto_id = katip_poi.schadenskonto_id) " .
            "WHERE katip_schadenskonten.map_id = :map_id " .
            "ORDER BY katip_poi.title ASC " .
        "");
        $statement->execute(array(
            'map_id' => $current_map->getId()
        ));
        $pois = array();
        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $poi_data) {
            $poi = new PointOfInterest();
            $poi->setData($poi_data);
            $poi->setNew(false);
            $pois[] = $poi;
        }
        return $pois;
    }
    
    static private function getLocalFiles($path, $shortpath) {
        $images = array();
        $handle = opendir($path);
        if (!$handle) {
            return $images;
        }
        while (false !== ($file = readdir($handle))) {
            if ($file[0] !== ".") { //means . & .. & .htaccess et cetera
                if (is_dir($path."/".$file)) {
                    $images[$shortpath."/".$file] = self::getLocalFiles($path."/".$file, $shortpath."/".$file);
                } else {
                    $images[$shortpath."/".$file] = $file;
                }
            }
        }
        return $images;
    }
    
    static public function getImages($course_id = null) {
        return self::getLocalFiles(dirname(__file__)."/../assets/markers", "");
    }
    
    public function __construct($id = null) {
        $this->registerCallback('before_store', 'serializeCoordinates');
        $this->registerCallback('after_store after_initialize', 'unserializeCoordinates');
        parent::__construct($id);
    }
    
    protected function serializeCoordinates() {
        $this->coordinates = json_encode(studip_utf8encode($this->coordinates));
    }
    
    protected function unserializeCoordinates() {
        $this->coordinates = studip_utf8decode(json_decode($this->coordinates));
    }
    
    public function setId($id) {
        $old_id = $this->getId();
        $success = parent::setId($id);
        if ($success && (($this['first_predecessor'] === $old_id) || !$this['first_predecessor'])) {
            $this['first_predecessor'] = $id;
        }
        return $success;
    }

    public function getLength() {
        if ($this['shape'] === "polyline") {
            $length = 0;
            $last_point = null;
            foreach ($this['coordinates'] as $coordinate) {
                if ($last_point !== null) {
                    $length += $this->getDistance(
                        $coordinate[1], $coordinate[0],
                        $last_point[1], $last_point[0]
                    );
                }
                $last_point = $coordinate;
            }
            return round($length, 3);
        } else {
            return 0;
        }
    }

    private function getDistance($lat1, $lng1, $lat2, $lng2) {
        $pi80 = M_PI / 180;
        $lat1 *= $pi80;
        $lng1 *= $pi80;
        $lat2 *= $pi80;
        $lng2 *= $pi80;

        $r = 6372.797; // mean radius of Earth in km
        $dlat = $lat2 - $lat1;
        $dlng = $lng2 - $lng1;
        $a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlng / 2) * sin($dlng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $km = $r * $c;

        return $km;
    }
}
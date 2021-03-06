<?php

/*
 *  Copyright (c) 2013  Rasmus Fuhse <fuhse@data-quest.de>
 *
 *  This program is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU General Public License as
 *  published by the Free Software Foundation; either version 2 of
 *  the License, or (at your option) any later version.
 */

require_once dirname(__file__)."/PointOfInterest.class.php";

class ExternalDataURL extends SimpleORMap
{

    static public function findBySeminar($course_id)
    {
        return self::findBySQL("Seminar_id = ? ORDER BY name ASC", array($course_id));
    }

    protected static function configure($config = array())
    {
        $config['db_table'] = 'katip_external_data_urls';
        $config['serialized_fields']['mapping'] = "JSONArrayObject";
        $config['serialized_fields']['last_object'] = "JSONArrayObject";
        parent::configure($config);
    }

    public function fetch()
    {
        if ($this['last_update'] + ($this['interval'] * 60) > time()) {
            return;
        }
        // cURL-Request:
        $c = curl_init();
        curl_setopt($c, CURLOPT_HTTPHEADER, array("Accept" => "application/json"));
        curl_setopt($c, CURLOPT_URL, $this['url']);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_VERBOSE, 0);
        curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 20);
        if ($this['auth_user']) {
            curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($c, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($c, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($c, CURLOPT_USERPWD,
                $this['auth_user'] . ':' . $this['auth_pw']
            );
        }
        curl_setopt($c, CURLOPT_HTTPGET, true);
        $result = curl_exec($c);
        $response_code = curl_getinfo($c, CURLINFO_HTTP_CODE);
        curl_close($c);
        if ($result === false) {
            curl_errno($c);
            curl_error($c);
        } else {
            $object = json_decode($result);
            if ($object) {
                $this['last_object'] = $object;
                $this['last_update'] = time();
                $this->store();
                $this->apply_mapping();
            }
        }
    }

    public function apply_mapping()
    {
        if (!$this['active']) {
            return;
        }
        //map new data to pois:
        foreach ($this['mapping'] as $path => $mapping_rule) {
            $poi = PointOfInterest::findCurrentByPoiID($mapping_rule['poi_id']);
            if ($poi) {
                $value = $this['last_object'];
                foreach (explode(" ", $path) as $path_unit) {
                    $value = $value[$path_unit];
                }
                if ($poi->isField($mapping_rule['poi_attribute'])) {
                    $poi[$mapping_rule['poi_attribute']] = $value;
                }
                if (in_array($poi['shape'], array("marker","circle")) && $mapping_rule['poi_attribute'] === "longitude") {
                    $poi['coordinates'] = array(
                        $value,
                        $poi['coordinates'][1]
                    );
                }
                if (in_array($poi['shape'], array("marker","circle")) && $mapping_rule['poi_attribute'] === "latitude") {
                    $poi['coordinates'] = array(
                        $poi['coordinates'][0],
                        $value
                    );
                }
                $poi->store();
            }
        }
    }
}

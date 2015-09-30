<?php

class PoiDatafield extends SimpleORMap {

    protected static function configure($config = array())
    {
        $config['db_table'] = 'katip_poi_datafields';
        $config['belongs_to']['poi'] = array(
            'class_name' => 'PointOfInterest'
        );
        parent::configure($config);
    }

}
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
}
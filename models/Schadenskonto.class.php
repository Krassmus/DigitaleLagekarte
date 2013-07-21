<?php
/*
 *  Copyright (c) 2012-2013  Rasmus Fuhse <fuhse@data-quest.de>
 *
 *  This program is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU General Public License as
 *  published by the Free Software Foundation; either version 2 of
 *  the License, or (at your option) any later version.
 */

class Schadenskonto extends SimpleORMap {
    protected $db_table = "katip_schadenskonten";
    
    public function setId($id) {
        $old_id = $this->getId();
        $success = parent::setId($id);
        if ($success && (($this['first_predecessor'] === $old_id) || !$this['first_predecessor'])) {
            $this['first_predecessor'] = $id;
        }
        return $success;
    }
    
    public function getPOIs() {
        return PointOfInterest::findBySQL("schadenskonto_id = ? ORDER BY title ASC", array($this->getId()));
    }
}
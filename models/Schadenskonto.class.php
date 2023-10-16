<?php
/*
 *  Copyright (c) 2012-2013  Rasmus Fuhse <fuhse@data-quest.de>
 *
 *  This program is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU General Public License as
 *  published by the Free Software Foundation; either version 2 of
 *  the License, or (at your option) any later version.
 */

class Schadenskonto extends SimpleORMap
{
    protected static function configure($config = array())
    {
        $config['db_table'] = 'katip_schadenskonten';
        $config['registered_callbacks']['before_create'][] = 'cbInitFirstPredecessor';
        parent::configure($config);
    }

    public function cbInitFirstPredecessor()
    {
        if (!$this['first_predecessor']) {
            if ($this['predecessor']) {
                $predecessor = static::find($this['predecessor']);
                $this['first_predecessor'] = $predecessor ?? $predecessor['first_predecessor'];
            } else {
                if (!$this->getId()) {
                    $this->setId(md5(uniqid()));
                }
                $this['first_predecessor'] = $this->getId();
            }
        }
    }

    public function getPOIs()
    {
        return PointOfInterest::findBySQL("schadenskonto_id = ? ORDER BY title ASC", array($this->getId()));
    }
}

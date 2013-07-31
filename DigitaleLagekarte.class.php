<?php
/*
 *  Copyright (c) 2012-2013  Rasmus Fuhse <fuhse@data-quest.de>
 *
 *  This program is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU General Public License as
 *  published by the Free Software Foundation; either version 2 of
 *  the License, or (at your option) any later version.
 */

require_once(dirname(__file__)."/models/Lagekarte.class.php");
require_once(dirname(__file__)."/models/Schadenskonto.class.php");
require_once(dirname(__file__)."/models/PointOfInterest.class.php");

class DigitaleLagekarte extends StudIPPlugin implements StandardPlugin {
    
    protected function getDisplayName() {
        return _("Lagekarte");
    }
    
    public function getNotificationObjects($course_id, $since, $user_id) {
        return null;
    }
    
    public function getTabNavigation($course_id) {
        $nav = new Navigation(_("Lagekarte"), PluginEngine::getURL($this, array(), "map/current"));
        $show = new AutoNavigation(_("Lagekarte"), PluginEngine::getURL($this, array(), "map/current"));
        $nav->addSubNavigation('show', $show);
        $schadenskonten = new AutoNavigation(_("Schadenskonten"), PluginEngine::getURL($this, array(), "schadenskonten/overview"));
        $nav->addSubNavigation('schadenskonten', $schadenskonten);
        
        if ($GLOBALS['auth']->auth['devicePixelRatio'] > 1.2) {
            $nav->setImage($this->getPluginURL()."/assets/32_white_world.png");
        } else {
            $nav->setImage($this->getPluginURL()."/assets/16_white_world.png");
        }
        
        return array('lagekarte' => $nav);
    }
    
    public function getInfoTemplate($course_id) {
        return null;
    }
    
    public function getIconNavigation($course_id, $last_visit, $user_id) {
        $nav = new AutoNavigation(_("Lagekarte"), PluginEngine::getURL($this, array(), "map/current"));
        if ($GLOBALS['auth']->auth['devicePixelRatio'] > 1.2) {
            $nav->setImage($this->getPluginURL()."/assets/32_grey_world.png", array('title' => _("Lagekarte"), 'width' => "16px"));
        } else {
            $nav->setImage($this->getPluginURL()."/assets/16_grey_world.png", array('title' => _("Lagekarte")));
        }
        return $nav;
    }
    
    /**
    * This method dispatches and displays all actions. It uses the template
    * method design pattern, so you may want to implement the methods #route
    * and/or #display to adapt to your needs.
    *
    * @param  string  the part of the dispatch path, that were not consumed yet
    *
    * @return void
    */
    public function perform($unconsumed_path) {
        if(!$unconsumed_path) {
            header("Location: " . PluginEngine::getUrl($this), 302);
            return false;
        }
        $trails_root = $this->getPluginPath();
        $dispatcher = new Trails_Dispatcher($trails_root, null, 'show');
        $dispatcher->current_plugin = $this;
        $dispatcher->dispatch($unconsumed_path);
    }
}

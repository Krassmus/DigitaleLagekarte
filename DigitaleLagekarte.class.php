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
require_once(dirname(__file__)."/models/ExternalDataURL.class.php");

class DigitaleLagekarte extends StudIPPlugin implements StandardPlugin {
    
    public function __construct() {
        parent::__construct();
        if (UpdateInformation::isCollecting()) {
            $data = Request::getArray("page_info");
            if ((stripos(Request::get("page"), "plugins.php/digitalelagekarte") !== false) 
                    && $data['Lagekarte']['current_map']) {
                $output = array();
                
                $old_map = new Lagekarte($data['Lagekarte']['map_id']);
                $new_map = Lagekarte::getCurrent($old_map['seminar_id']);
                $output['map_id'] = $new_map->getId();
                $output['poi_ids'] = array();
                $tf = new Flexi_TemplateFactory(dirname(__file__)."/views");
                foreach ($new_map->getSchadenskonten() as $schadenskonto) {
                    foreach ($schadenskonto->getPOIs() as $poi) {
                        if ($poi['chdate'] >= $data['Lagekarte']['last_update']) {
                            $popup_template = $tf->open("map/_poi_popup.php");
                            $popup_template->set_attribute('plugin', $this);
                            $popup_template->set_attribute('poi', $poi);
                            $output['poi'][] = array(
                                'poi_id' => $poi->getId(),
                                'type' => $poi['shape'],
                                'image' => $poi['image'],
                                'coordinates' => $poi['coordinates'],
                                'radius' => $poi['radius'],
                                'color' => $poi['color'],
                                'predecessor' => $poi['predecessor'],
                                'popup' => $popup_template->render()
                            );
                        }
                        $output['poi_ids'][] = $poi->getId();
                    }
                }
                UpdateInformation::setInformation("Lagekarte.updateMap", $output);
            }
        }
        
        /* pseudo-cronjob after 10 minutes */
        $urls = ExternalDataURL::findBySQL("active = 1 AND last_update < UNIX_TIMESTAMP() - (60 * 10)");
        foreach ($urls as $url) {
            $url->fetch();
        }
    }
    
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
        if ($GLOBALS['perm']->have_studip_perm("tutor", $course_id)) {
            $schadenskonten = new AutoNavigation(_("Externe Daten"), PluginEngine::getURL($this, array(), "externedaten/overview"));
            $nav->addSubNavigation('externedaten', $schadenskonten);
        }
        
        if ($GLOBALS['auth']->auth['devicePixelRatio'] > 1.2) {
            $nav->setImage($this->getPluginURL()."/assets/40_white_world.png");
        } else {
            $nav->setImage($this->getPluginURL()."/assets/20_white_world.png");
        }
        
        return array('lagekarte' => $nav);
    }
    
    public function getInfoTemplate($course_id) {
        return null;
    }
    
    public function getIconNavigation($course_id, $last_visit, $user_id) {
        $nav = new AutoNavigation(_("Lagekarte"), PluginEngine::getURL($this, array(), "map/current"));
        if ($GLOBALS['auth']->auth['devicePixelRatio'] > 1.2) {
            $nav->setImage($this->getPluginURL()."/assets/40_grey_world.png", array('title' => _("Lagekarte"), 'width' => "20px"));
        } else {
            $nav->setImage($this->getPluginURL()."/assets/20_grey_world.png", array('title' => _("Lagekarte")));
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

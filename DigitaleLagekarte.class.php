<?php
/*
 *  Copyright (c) 2012-2013  Rasmus Fuhse <fuhse@data-quest.de>
 *
 *  This program is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU General Public License as
 *  published by the Free Software Foundation; either version 2 of
 *  the License, or (at your option) any later version.
 */

require_once(__DIR__."/models/Lagekarte.class.php");
require_once(__DIR__."/models/Schadenskonto.class.php");
require_once(__DIR__."/models/PointOfInterest.class.php");
require_once(__DIR__."/models/PoiDatafield.class.php");
require_once(__DIR__."/models/ExternalDataURL.class.php");

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
                $output['longitude'] = $new_map['longitude'];
                $output['latitude'] = $new_map['latitude'];
                $output['zoom'] = $new_map['zoom'];
                $output['poi_ids'] = array();
                $tf = new Flexi_TemplateFactory(dirname(__file__)."/views");
                $images = PointOfInterest::getImages($old_map['seminar_id']);
                foreach ($new_map->getSchadenskonten() as $schadenskonto) {
                    foreach ($schadenskonto->getPOIs() as $poi) {
                        if ($poi['chdate'] >= $data['Lagekarte']['last_update']) {
                            $popup_template = $tf->open("map/_poi_popup.php");
                            $popup_template->set_attribute('plugin', $this);
                            $popup_template->set_attribute('poi', $poi);
                            $popup_template->set_attribute('images', $images);
                            $output['poi'][] = array(
                                'poi_id' => $poi->getId(),
                                'type' => $poi['shape'],
                                'image' => $poi['image'],
                                'coordinates' => $poi['coordinates'],
                                'radius' => $poi['radius'],
                                'color' => $poi['color'],
                                'size' => $poi['size'],
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
        /*
        $urls = ExternalDataURL::findBySQL("active = 1 AND last_update <= UNIX_TIMESTAMP() - (60 + 30)");
        foreach ($urls as $url) {
            @$url->fetch();
        }
        */
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
        
        $nav->setImage(Icon::create("globe", "info"));

        return array('lagekarte' => $nav);
    }
    
    public function getInfoTemplate($course_id) {
        return null;
    }
    
    public function getIconNavigation($course_id, $last_visit, $user_id) {
        $nav = new AutoNavigation(_("Lagekarte"), PluginEngine::getURL($this, array(), "map/current"));
        if ($GLOBALS['auth']->auth['devicePixelRatio'] > 1.2) {
            $nav->setImage(Icon::create($this->getPluginURL()."/assets/40_grey_world.png"));
        } else {
            $nav->setImage(Icon::create($this->getPluginURL()."/assets/20_grey_world.png"));
        }
        return $nav;
    }
}

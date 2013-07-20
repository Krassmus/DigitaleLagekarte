<?php
/*
 *  Copyright (c) 2012-2013  Rasmus Fuhse <fuhse@data-quest.de>
 *
 *  This program is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU General Public License as
 *  published by the Free Software Foundation; either version 2 of
 *  the License, or (at your option) any later version.
 */

require_once(dirname(__file__)."/lib/Lagekarte.class.php");

class DigitaleLagekarte extends StudIPPlugin implements StandardPlugin {
    
    protected function getDisplayName() {
        return _("Lagekarte");
    }
    
    public function getNotificationObjects($course_id, $since, $user_id) {
        return null;
    }
    
    public function getTabNavigation($course_id) {
        $nav = new AutoNavigation(_("Lagekarte"), PluginEngine::getURL($this, array(), "show"));
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
        $nav = new AutoNavigation(_("Lagekarte"), PluginEngine::getURL($this, array(), "show"));
        if ($GLOBALS['auth']->auth['devicePixelRatio'] > 1.2) {
            $nav->setImage($this->getPluginURL()."/assets/32_grey_world.png", array('title' => _("Lagekarte"), 'width' => "16px"));
        } else {
            $nav->setImage($this->getPluginURL()."/assets/16_grey_world.png", array('title' => _("Lagekarte")));
        }
        return $nav;
    }
    
    public function show_action() {
        PageLayout::addHeadElement("script", array('src' => $this->getPluginURL()."/assets/Leaflet/leaflet.js"), "");
        PageLayout::addHeadElement("script", array('src' => $this->getPluginURL()."/assets/Leaflet/leaflet.draw.js"), "");
        PageLayout::addHeadElement("link", array('href' => $this->getPluginURL()."/assets/Leaflet/leaflet.css", 'rel' => "stylesheet"));
        PageLayout::addHeadElement("link", array('href' => $this->getPluginURL()."/assets/Leaflet/leaflet.draw.css", 'rel' => "stylesheet"));
        PageLayout::addHeadElement("script", array('src' => $this->getPluginURL()."/assets/application.js"), "");
        if ($GLOBALS['auth']->auth['devicePixelRatio'] > 1.2) {
            Navigation::getItem("/course/lagekarte")->setImage($this->getPluginURL()."/assets/32_black_world.png");
        } else {
            Navigation::getItem("/course/lagekarte")->setImage($this->getPluginURL()."/assets/16_black_world.png");
        }
        $map = Lagekarte::getCurrent($_SESSION['SessionSeminar']);
        if ($map->isNew()) {
            $map['seminar_id'] = $_SESSION['SessionSeminar'];
            $map['latitude'] = 53.152692;
            $map['longitude'] = 8.187937;
            $map['zoom'] = 17;
            $map['user_id'] = "";
        }
        
        $template = $this->getTemplate("lagekarte.php", "with_infobox");
        $template->set_attribute("map", $map);
        $template->set_attribute("plugin", $this);
        echo $template->render();
    }
    
    public function edit_map_action() {
        if (!$GLOBALS['perm']->have_studip_perm("tutor", $_SESSION['SessionSeminar'])) {
            throw new AccessDeniedException("Kein Zugriff");
        }
        Navigation::activateItem("/course/lagekarte");
        PageLayout::addHeadElement("script", array('src' => $this->getPluginURL()."/assets/Leaflet/leaflet.js"), "");
        PageLayout::addHeadElement("script", array('src' => $this->getPluginURL()."/assets/Leaflet/leaflet.draw.js"), "");
        PageLayout::addHeadElement("link", array('href' => $this->getPluginURL()."/assets/Leaflet/leaflet.css", 'rel' => "stylesheet"));
        PageLayout::addHeadElement("link", array('href' => $this->getPluginURL()."/assets/Leaflet/leaflet.draw.css", 'rel' => "stylesheet"));
        PageLayout::addHeadElement("script", array('src' => $this->getPluginURL()."/assets/application.js"), "");
        if ($GLOBALS['auth']->auth['devicePixelRatio'] > 1.2) {
            Navigation::getItem("/course/lagekarte")->setImage($this->getPluginURL()."/assets/32_black_world.png");
        } else {
            Navigation::getItem("/course/lagekarte")->setImage($this->getPluginURL()."/assets/16_black_world.png");
        }
        
        $map = Lagekarte::getCurrent($_SESSION['SessionSeminar']);
        if ($map->isNew()) {
            $map['seminar_id'] = $_SESSION['SessionSeminar'];
            $map['latitude'] = 53.152692;
            $map['longitude'] = 8.187937;
            $map['zoom'] = 17;
        }
        
        $template = $this->getTemplate("lagekarte_edit.php", "with_infobox");
        $template->set_attribute("map", $map);
        $template->set_attribute("plugin", $this);
        echo $template->render();
    }
    
    public function save_map_viewport_action() {
        if (!$GLOBALS['perm']->have_studip_perm("tutor", $_SESSION['SessionSeminar'])) {
            throw new AccessDeniedException("Kein Zugriff");
        }
        $map = Lagekarte::getCurrent($_SESSION['SessionSeminar']);
        $new_map = Lagekarte::copyFrom($map);
        $new_map['seminar_id'] = $_SESSION['SessionSeminar'];
        $new_map['longitude'] = Request::float("longitude");
        $new_map['latitude'] = Request::float("latitude");
        $new_map['zoom'] = Request::int("zoom");
        $new_map['user_id'] = $GLOBALS['user']->id;
        $new_map->store();
    }
    
    protected function getTemplate($template_file_name, $layout = "without_infobox") {
        if (!$this->template_factory) {
            $this->template_factory = new Flexi_TemplateFactory(dirname(__file__)."/templates");
        }
        $template = $this->template_factory->open($template_file_name);
        if ($layout) {
            if (method_exists($this, "getDisplayName")) {
                PageLayout::setTitle($this->getDisplayName());
            } else {
                PageLayout::setTitle(get_class($this));
            }
            $template->set_layout($GLOBALS['template_factory']->open($layout === "without_infobox" ? 'layouts/base_without_infobox' : 'layouts/base'));
        }
        return $template;
    }
}

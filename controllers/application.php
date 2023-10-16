<?
/*
 *  Copyright (c) 2010 André Noack <noack@data-quest.de>
 *  Copyright (c) 2012 Rasmus Fuhse <fuhse@data-quest.de>
 *
 *  This program is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU General Public License as
 *  published by the Free Software Foundation; either version 2 of
 *  the License, or (at your option) any later version.
 */

/**
 * Special controller for trailsplugins
 */
class ApplicationController extends PluginController {

    /**
     * Sets layout, adds css
     * @param type $action
     * @param type $args
     */
    function before_filter(&$action, &$args) {
        parent::before_filter($action, $args);
        PageLayout::addStylesheet($this->plugin->getPluginURL()."/assets/lagekarte.css");
        PageLayout::addHeadElement("script", array('src' => $this->plugin->getPluginURL()."/assets/Leaflet/leaflet.js"), "");
        PageLayout::addHeadElement("script", array('src' => $this->plugin->getPluginURL()."/assets/Leaflet/leaflet.draw.js"), "");
        PageLayout::addHeadElement("link", array('href' => $this->plugin->getPluginURL()."/assets/Leaflet/leaflet.css", 'rel' => "stylesheet"));
        PageLayout::addHeadElement("link", array('href' => $this->plugin->getPluginURL()."/assets/Leaflet/leaflet.draw.css", 'rel' => "stylesheet"));
        PageLayout::addHeadElement("script", array('src' => $this->plugin->getPluginURL()."/assets/lagekarte.js"), "");
    }

}

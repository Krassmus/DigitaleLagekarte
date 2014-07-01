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

require_once 'app/models/plugin_administration.php';
require_once 'app/controllers/plugin_controller.php';
/**
 * Special controller for trailsplugins
 */
class ApplicationController extends PluginController {

    /**
     * Sets layout, adds css
     * @param type $action
     * @param type $args
     */
    function before_filter($action, $args) {
        parent::before_filter($action, $args);
        $version = shell_exec("cd ".__DIR__." && git log -1 --pretty=format:'%h' --abbrev-commit");
        if (!$version) {
            $version = PluginManager::getInstance()->getPluginInfo("DigitaleLagekarte");
            $manifest = PluginManager::getInstance()->getPluginManifest($GLOBALS['PLUGINS_PATH']."/".$version['path']);
            $version = $manifest['version'];
        }
        $version = $version ? '?version='.urlencode($version) : "";
        PageLayout::addStylesheet($this->plugin->getPluginURL()."/assets/lagekarte.css".$version);
        PageLayout::addHeadElement("script", array('src' => $this->plugin->getPluginURL()."/assets/Leaflet/leaflet.js".$version), "");
        PageLayout::addHeadElement("script", array('src' => $this->plugin->getPluginURL()."/assets/Leaflet/leaflet.draw.js".$version), "");
        PageLayout::addHeadElement("link", array('href' => $this->plugin->getPluginURL()."/assets/Leaflet/leaflet.css".$version, 'rel' => "stylesheet"));
        PageLayout::addHeadElement("link", array('href' => $this->plugin->getPluginURL()."/assets/Leaflet/leaflet.draw.css".$version, 'rel' => "stylesheet"));
        PageLayout::addHeadElement("link", array('href' => $this->plugin->getPluginURL()."/assets/Leaflet/Control.FullScreen.css".$version, 'rel' => "stylesheet"));
        PageLayout::addHeadElement("script", array('src' => $this->plugin->getPluginURL()."/assets/Leaflet/Control.FullScreen.js".$version), "");
        PageLayout::addHeadElement("script", array('src' => $this->plugin->getPluginURL()."/assets/lagekarte.js".$version), "");
    }

}

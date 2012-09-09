<?php

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
        return array('lagekarte' => $nav);
    }
    
    public function getInfoTemplate($course_id) {
        return null;
    }
    
    public function getIconNavigation($course_id, $last_visit, $user_id) {
        return null;
    }
    
    public function show_action() {
        PageLayout::addHeadElement("script", array('src' => $this->getPluginURL()."/assets/OpenLayers/OpenLayers.js"), "");
        PageLayout::addHeadElement("script", array('src' => $this->getPluginURL()."/assets/application.js"), "");
        
        $map = Lagekarte::getCurrent($_SESSION['SessionSeminar']);
        if ($map->isNew()) {
            $map['seminar_id'] = $_SESSION['SessionSeminar'];
            $map['latitude'] = 53.152692;
            $map['longitude'] = 8.187937;
            $map['zoom'] = 17;
            $map['user_id'] = "";
            $map->store();
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
        PageLayout::addHeadElement("script", array('src' => $this->getPluginURL()."/assets/OpenLayers/OpenLayers.js"), "");
        PageLayout::addHeadElement("script", array('src' => $this->getPluginURL()."/assets/application.js"), "");
        
        $map = Lagekarte::getCurrent($_SESSION['SessionSeminar']);
        if ($map->isNew()) {
            $map['seminar_id'] = $_SESSION['SessionSeminar'];
            $map['latitude'] = 53.152692;
            $map['longitude'] = 8.187937;
            $map['zoom'] = 17;
            $map->store();
        }
        
        $template = $this->getTemplate("lagekarte_edit.php", "with_infobox");
        $template->set_attribute("map", $map);
        $template->set_attribute("plugin", $this);
        echo $template->render();
    }
    
    public function save_map_action() {
        if (!$GLOBALS['perm']->have_studip_perm("tutor", $_SESSION['SessionSeminar'])) {
            throw new AccessDeniedException("Kein Zugriff");
        }
        $map = Lagekarte::getCurrent($_SESSION['SessionSeminar']);
        $new_map = Lagekarte::copyFrom($map);
        $new_map['longitude'] = Request::float("longitude");
        $new_map['latitude'] = Request::float("latitude");
        $new_map['zoom'] = Request::int("zoom");
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

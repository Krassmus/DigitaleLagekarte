<?php

require_once dirname(__file__)."/application.php";

class MapController extends ApplicationController {
    
    public function show_action() {
        PageLayout::addHeadElement("script", array('src' => $this->assets_url."Leaflet/leaflet.js"), "");
        PageLayout::addHeadElement("script", array('src' => $this->assets_url."Leaflet/leaflet.draw.js"), "");
        PageLayout::addHeadElement("link", array('href' => $this->assets_url."Leaflet/leaflet.css", 'rel' => "stylesheet"));
        PageLayout::addHeadElement("link", array('href' => $this->assets_url."Leaflet/leaflet.draw.css", 'rel' => "stylesheet"));
        PageLayout::addHeadElement("script", array('src' => $this->assets_url."application.js"), "");
        if ($GLOBALS['auth']->auth['devicePixelRatio'] > 1.2) {
            Navigation::getItem("/course/lagekarte")->setImage($this->assets_url."32_black_world.png");
        } else {
            Navigation::getItem("/course/lagekarte")->setImage($this->assets_url."16_black_world.png");
        }
        $this->map = Lagekarte::getCurrent($_SESSION['SessionSeminar']);
        if ($this->map->isNew()) {
            //set default map to center OV Oldenburg
            $this->map['seminar_id'] = $_SESSION['SessionSeminar'];
            $this->map['latitude'] = 53.152692;
            $this->map['longitude'] = 8.187937;
            $this->map['zoom'] = 17;
            $this->map['user_id'] = "";
        }
    }
    
    public function edit_action() {
        if (!$GLOBALS['perm']->have_studip_perm("tutor", $_SESSION['SessionSeminar'])) {
            throw new AccessDeniedException("Kein Zugriff");
        }
        $this->show_action();
        Navigation::activateItem("/course/lagekarte/show");
    }
    
    public function save_viewport_action() {
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
        
        $this->render_nothing();
    }
    
}
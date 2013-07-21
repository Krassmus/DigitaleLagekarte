<?php

require_once dirname(__file__)."/application.php";

class MapController extends ApplicationController {
    
    public function show_action() {
        PageLayout::addHeadElement("script", array('src' => $this->assets_url."Leaflet/leaflet.js"), "");
        PageLayout::addHeadElement("script", array('src' => $this->assets_url."Leaflet/leaflet.draw.js"), "");
        PageLayout::addHeadElement("link", array('href' => $this->assets_url."Leaflet/leaflet.css", 'rel' => "stylesheet"));
        PageLayout::addHeadElement("link", array('href' => $this->assets_url."Leaflet/leaflet.draw.css", 'rel' => "stylesheet"));
        PageLayout::addHeadElement("script", array('src' => $this->assets_url."lagekarte.js"), "");
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
            $this->schadenskonten = array();
        } else {
            $this->schadenskonten =  $this->map->getSchadenskonten();
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
    
    public function save_new_layer_action() {
        if (!$GLOBALS['perm']->have_studip_perm("tutor", $_SESSION['SessionSeminar'])) {
            throw new AccessDeniedException("Kein Zugriff");
        }
        $map = Lagekarte::getCurrent($_SESSION['SessionSeminar']);
        $output = array();
        if (Request::get("schadenskonto_id") !== "neu") {
            $schadenskonto = Schadenskonto::find(Request::get("schadenskonto_id"));
        } elseif(Request::get("schadenskonto_title")) {
            $schadenskonto = new Schadenskonto();
            $schadenskonto['title'] = studip_utf8decode(Request::get("schadenskonto_title"));
            $schadenskonto['map_id'] = $map->getId();
            $success = $schadenskonto->store();
            if ($success) {
                $output['new_schadenskonto'] = array('id' => $schadenskonto->getId(), 'name' => $schadenskonto['title']);
            }
        }
        $object = new PointOfInterest();
        $object['schadenskonto_id'] = $schadenskonto->getId();
        $object['coordinates'] = Request::getArray("coordinates");
        $object['radius'] = Request::get("radius") ? Request::get("radius") : null;
        $object['shape'] = Request::get("shape");
        $object['image'] = Request::get("image");
        $object['title'] = studip_utf8decode(Request::get("title"));
        $success = $object->store();
        if ($success) {
            $output['new_poi'] = array('id' => $object->getId());
        }
        $this->render_json($output);
    }
    
}
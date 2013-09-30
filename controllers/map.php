<?php

require_once dirname(__file__)."/application.php";

class MapController extends ApplicationController {
    
    public function current_action() {
        if ($GLOBALS['auth']->auth['devicePixelRatio'] > 1.2) {
            Navigation::getItem("/course/lagekarte")->setImage($this->assets_url."40_black_world.png");
        } else {
            Navigation::getItem("/course/lagekarte")->setImage($this->assets_url."20_black_world.png");
        }
        $this->map = Lagekarte::getCurrent($_SESSION['SessionSeminar']);
        if ($this->map->isNew()) {
            $this->schadenskonten = array();
        } else {
            $this->schadenskonten =  $this->map->getSchadenskonten();
        }
    }
    
    public function save_viewport_action() {
        if (!$GLOBALS['perm']->have_studip_perm("tutor", $_SESSION['SessionSeminar'])) {
            throw new AccessDeniedException("Kein Zugriff");
        }
        $map = Lagekarte::getCurrent($_SESSION['SessionSeminar']);
        $map['seminar_id'] = $_SESSION['SessionSeminar'];
        $map['longitude'] = Request::float("longitude");
        $map['latitude'] = Request::float("latitude");
        $map['zoom'] = Request::int("zoom");
        $map['user_id'] = $GLOBALS['user']->id;
        $map->store();
        
        $this->render_nothing();
    }
    
    public function create_snapshot_action() {
        if (!$GLOBALS['perm']->have_studip_perm("tutor", $_SESSION['SessionSeminar'])) {
            throw new AccessDeniedException("Kein Zugriff");
        }
        $map = Lagekarte::getCurrent($_SESSION['SessionSeminar']);
        $new_map = $map->createCopy();
        $this->render_nothing();
    }
    
    public function save_new_layer_action() {
        if (!$GLOBALS['perm']->have_studip_perm("tutor", $_SESSION['SessionSeminar'])) {
            throw new AccessDeniedException("Kein Zugriff");
        }
        $map = Lagekarte::getCurrent($_SESSION['SessionSeminar']);
        if ($map->isNew()) {
            $map->store();
        }
        
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
        $object['visible'] = 1;
        $object['title'] = studip_utf8decode(Request::get("title"));
        $success = $object->store();
        if ($success) {
            $popup_template = $this->get_template_factory()->open("map/_poi_popup.php");
            $popup_template->set_attribute('plugin', $this->plugin);
            $popup_template->set_attribute('poi', $object);
            $output['new_poi'] = array(
                'id' => $object->getId(),
                'popup' => $popup_template->render()
            );
        }
        $this->render_json($output);
    }
    
    public function delete_poi_action() {
        if (!$GLOBALS['perm']->have_studip_perm("tutor", $_SESSION['SessionSeminar'])) {
            throw new AccessDeniedException("Kein Zugriff");
        }
        $map = Lagekarte::getCurrent($_SESSION['SessionSeminar']);
        foreach (Request::optionArray("poi_ids") as $poi_id) {
            $poi = new PointOfInterest($poi_id);
            if (Schadenskonto::find($poi['schadenskonto_id'])->map_id === $map->getId()) {
                $poi->delete();
            }
        }
        $this->render_nothing();
    }
    
    public function edit_poi_action() {
        if (!$GLOBALS['perm']->have_studip_perm("tutor", $_SESSION['SessionSeminar']) || !Request::isPost()) {
            throw new AccessDeniedException("Kein Zugriff");
        }
        $map = Lagekarte::getCurrent($_SESSION['SessionSeminar']);
        foreach (Request::optionArray("poi") as $poi_id => $attributes) {
            $poi = new PointOfInterest($poi_id);
            if (Schadenskonto::find($poi['schadenskonto_id'])->map_id === $map->getId()) {
                $poi['radius'] = $attributes['radius'];
                $poi['coordinates'] = $attributes['coordinates'];
                $poi->store();
            }
        }
        $this->render_nothing();
    }
    
    public function edit_poi_color_action() {
        if (!$GLOBALS['perm']->have_studip_perm("tutor", $_SESSION['SessionSeminar']) || !Request::isPost()) {
            throw new AccessDeniedException("Kein Zugriff");
        }
        $map = Lagekarte::getCurrent($_SESSION['SessionSeminar']);
        $poi = new PointOfInterest(Request::option("poi_id"));
        if (Schadenskonto::find($poi['schadenskonto_id'])->map_id === $map->getId()) {
            $poi['color'] = Request::get("color");
            $poi->store();
        }
        $this->render_nothing();
    }
    
}
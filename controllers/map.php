<?php

require_once dirname(__file__)."/application.php";

class MapController extends ApplicationController {

    public function current_action() {
        $this->map = Lagekarte::getCurrent(Context::get()->id);
        if (Request::isPost()) {
            if (Request::submitted("alert_window_text")) {
                $this->map['alert_window_text'] = trim(Request::get("alert_window_text"));
                $this->map->store();
                PageLayout::postMessage(MessageBox::success(_("Hinweismeldung wurde aktualisiert.")));
            }
        }
        if ($this->map->isNew()) {
            $this->schadenskonten = array();
        } else {
            $this->schadenskonten =  $this->map->getSchadenskonten();
        }
        $this->images = PointOfInterest::getImages(Context::get()->id);
    }

    public function save_viewport_action() {
        if (!$GLOBALS['perm']->have_studip_perm("tutor", Context::get()->id)) {
            throw new AccessDeniedException("Kein Zugriff");
        }
        $map = Lagekarte::getCurrent(Context::get()->id);
        $map['seminar_id'] = Context::get()->id;
        $map['longitude'] = Request::float("longitude");
        $map['latitude'] = Request::float("latitude");
        $map['zoom'] = Request::int("zoom");
        $map['user_id'] = $GLOBALS['user']->id;
        $map->store();

        $this->render_nothing();
    }

    public function create_snapshot_action() {
        if (!$GLOBALS['perm']->have_studip_perm("tutor", Context::get()->id)) {
            throw new AccessDeniedException("Kein Zugriff");
        }
        $map = Lagekarte::getCurrent(Context::get()->id);
        $new_map = $map->createCopy();
        $this->render_nothing();
    }

    public function save_new_layer_action() {
        if (!$GLOBALS['perm']->have_studip_perm("tutor", Context::get()->id)) {
            throw new AccessDeniedException("Kein Zugriff");
        }

        $map = Lagekarte::getCurrent(Context::get()->id);
        if ($map->isNew()) {
            $map['user_id'] = $GLOBALS['user']->id;
            $map->store();
        }

        $output = array();
        if (Request::get("schadenskonto_id") !== "neu") {
            $schadenskonto = Schadenskonto::find(Request::get("schadenskonto_id"));
        } elseif(Request::get("schadenskonto_title")) {
            $schadenskonto = new Schadenskonto();
            $schadenskonto['title'] = Request::get("schadenskonto_title");
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
        $object['title'] = Request::get("title");
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
        if (!$GLOBALS['perm']->have_studip_perm("tutor", Context::get()->id)) {
            throw new AccessDeniedException("Kein Zugriff");
        }
        $map = Lagekarte::getCurrent(Context::get()->id);
        foreach (Request::optionArray("poi_ids") as $poi_id) {
            $poi = new PointOfInterest($poi_id);
            if (Schadenskonto::find($poi['schadenskonto_id'])->map_id === $map->getId()) {
                $poi->delete();
            }
        }
        $this->render_nothing();
    }

    public function edit_poi_action() {
        if (!$GLOBALS['perm']->have_studip_perm("tutor", Context::get()->id) || !Request::isPost()) {
            throw new AccessDeniedException("Kein Zugriff");
        }
        $map = Lagekarte::getCurrent(Context::get()->id);
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
        if (!$GLOBALS['perm']->have_studip_perm("tutor", Context::get()->id) || !Request::isPost()) {
            throw new AccessDeniedException("Kein Zugriff");
        }
        $map = Lagekarte::getCurrent(Context::get()->id);
        $poi = new PointOfInterest(Request::option("poi_id"));
        if (Schadenskonto::find($poi['schadenskonto_id'])->map_id === $map->getId()) {
            $poi['color'] = Request::get("color");
            $poi->store();
        }
        $this->render_nothing();
    }

    public function edit_poi_attribute_action() {
        if (!$GLOBALS['perm']->have_studip_perm("tutor", Context::get()->id) || !Request::isPost()) {
            throw new AccessDeniedException("Kein Zugriff");
        }
        $map = Lagekarte::getCurrent(Context::get()->id);
        $poi = new PointOfInterest(Request::option("poi_id"));
        if (Schadenskonto::find($poi['schadenskonto_id'])->map_id === $map->getId()) {
            if (Request::submitted("color")) {
                $poi['color'] = Request::get("color");
            }
            if (Request::submitted("image")) {
                $poi['image'] = Request::get("image");
            }
            if (Request::submitted("size")) {
                $poi['size'] = Request::int("size");
            }
            $poi->store();
        }
        $this->render_nothing();
    }

    public function save_poi_datafield_action() {
        if (!$GLOBALS['perm']->have_studip_perm("tutor", Context::get()->id) || !Request::isPost()) {
            throw new AccessDeniedException("Kein Zugriff");
        }
        if (Request::get("name") && Request::option("poi_id")) {
            $poi_datafield = new PoiDatafield(Request::option("datafield_id"));
            if ($poi_datafield->isNew()) {
                $poi_datafield['poi_id'] = Request::option("poi_id");
            }
            $poi_datafield['name'] = Request::get("name");
            $poi_datafield['content'] = Request::get("content", "");
            $poi_datafield->store();
        } elseif (Request::option("datafield_id")) {
            $poi_datafield = new PoiDatafield(Request::option("datafield_id"));
            $poi_datafield->delete();
        }

        $output = array(
            'datafield_id' => $poi_datafield->getId()
        );
        $this->render_json($output);
    }

    public function edit_alert_window_action()
    {
        if (!$GLOBALS['perm']->have_studip_perm("tutor", Context::get()->id)) {
            throw new AccessDeniedException("Kein Zugriff");
        }
        $this->map = Lagekarte::getCurrent(Context::get()->id);

        if (Request::isXhr()) {
            $this->set_layout(null);
            $this->response->add_header('X-Title', _("Meldung bearbeiten"));
        }
    }



}

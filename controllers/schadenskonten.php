<?php

require_once dirname(__file__)."/application.php";

class SchadenskontenController extends ApplicationController
{

    public function overview_action() {
        $this->map = Lagekarte::getCurrent(Context::get()->id);
        $this->schadenskonten = $this->map->getSchadenskonten();
    }

    public function konto_action($id)
    {
        $this->schadenskonto = new Schadenskonto($id);
        $this->map = new Lagekarte($this->schadenskonto['map_id']);
        $this->schadenskonten = $this->map->getSchadenskonten();
        if ($this->schadenskonto->isNew()
                || $this->map->isNew()
                || !$GLOBALS['perm']->have_studip_perm("autor", $this->map['seminar_id'])) {
            throw new AccessDeniedException("Kein Zugriff");
        }
        Navigation::activateItem("/course/lagekarte/schadenskonten");
    }

    public function move_poi_to_schadenskonto_action()
    {
        $schadenskonto = new Schadenskonto(Request::option("schadenskonto_id"));
        $poi = new PointOfInterest(Request::option("poi_id"));
        $old_schadenskonto = new Schadenskonto($poi['schadenskonto_id']);
        $new_map = new Lagekarte($schadenskonto['map_id']);
        $old_map = new Lagekarte($old_schadenskonto['map_id']);
        if (!$GLOBALS['perm']->have_studip_perm("tutor", Context::get()->id)
                || !Request::isPost()
                || $new_map['Seminar_id'] !== Context::get()->id
                || $old_map['Seminar_id'] !== Context::get()->id) {
            throw new AccessDeniedException("kein Zugriff");
        }
        $poi['schadenskonto_id'] = $schadenskonto->getId();
        $poi->store();
        $this->render_nothing();
    }

    public function edit_action()
    {
        $schadenskonto = new Schadenskonto(Request::option("schadenskonto_id"));
        $schadenskonto[Request::get("attribute")] = Request::get("value");
        $schadenskonto->store();
        $this->render_text(formatReady($schadenskonto[Request::get("attribute")]));
    }

}

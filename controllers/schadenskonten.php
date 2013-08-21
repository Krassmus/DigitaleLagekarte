<?php

require_once dirname(__file__)."/application.php";

class SchadenskontenController extends ApplicationController {
    
    public function before_filter($action, $args) {
        parent::before_filter($action, $args);
        if ($GLOBALS['auth']->auth['devicePixelRatio'] > 1.2) {
            Navigation::getItem("/course/lagekarte")->setImage($this->assets_url."32_black_world.png");
        } else {
            Navigation::getItem("/course/lagekarte")->setImage($this->assets_url."16_black_world.png");
        }
    }
    
    public function overview_action() {
        $this->map = Lagekarte::getCurrent($_SESSION['SessionSeminar']);
        $this->schadenskonten = $this->map->getSchadenskonten();
    }
    
    public function konto_action($id) {
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
    
    public function move_poi_to_schadenskonto_action() {
        $schadenskonto = new Schadenskonto(Request::option("schadenskonto_id"));
        $poi = new PointOfInterest(Request::option("poi_id"));
        $old_schadenskonto = new Schadenskonto($poi['schadenskonto_id']);
        $new_map = new Lagekarte($schadenskonto['map_id']);
        $old_map = new Lagekarte($old_schadenskonto['map_id']);
        if (!$GLOBALS['perm']->have_studip_perm("tutor", $_SESSION['SessionSeminar']) 
                || !Request::isPost()
                || $new_map['Seminar_id'] !== $_SESSION['SessionSeminar']
                || $old_map['Seminar_id'] !== $_SESSION['SessionSeminar']) {
            throw new AccessDeniedException("kein Zugriff");
        }
        $poi['schadenskonto_id'] = $schadenskonto->getId();
        $poi->store();
        $this->render_nothing();
    }
    
}
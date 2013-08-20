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
    
}
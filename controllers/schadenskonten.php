<?php

require_once dirname(__file__)."/application.php";

class SchadenskontenController extends ApplicationController {
    
    public function overview_action() {
        PageLayout::addHeadElement("script", array('src' => $this->assets_url."application.js"), "");
        if ($GLOBALS['auth']->auth['devicePixelRatio'] > 1.2) {
            Navigation::getItem("/course/lagekarte")->setImage($this->assets_url."32_black_world.png");
        } else {
            Navigation::getItem("/course/lagekarte")->setImage($this->assets_url."16_black_world.png");
        }
        $this->map = Lagekarte::getCurrent($_SESSION['SessionSeminar']);
        //$this->schadenskonten = $this->map->getSchadenskonten();
    }
    
}
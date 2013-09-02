<?php

require_once dirname(__file__)."/application.php";

class ExternedatenController extends ApplicationController {
    
    public function before_filter($action, $args) {
        parent::before_filter($action, $args);
        if ($GLOBALS['auth']->auth['devicePixelRatio'] > 1.2) {
            Navigation::getItem("/course/lagekarte")->setImage($this->assets_url."32_black_world.png");
        } else {
            Navigation::getItem("/course/lagekarte")->setImage($this->assets_url."16_black_world.png");
        }
    }
    
    public function overview_action() {
        $this->urls = ExternalDataURL::findBySeminar($_SESSION['SessionSeminar']);
    }
    
}
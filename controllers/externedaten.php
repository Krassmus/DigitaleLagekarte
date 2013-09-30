<?php

require_once dirname(__file__)."/application.php";

class ExternedatenController extends ApplicationController {
    
    public function before_filter($action, $args) {
        parent::before_filter($action, $args);
        if ($GLOBALS['auth']->auth['devicePixelRatio'] > 1.2) {
            Navigation::getItem("/course/lagekarte")->setImage($this->assets_url."40_black_world.png");
        } else {
            Navigation::getItem("/course/lagekarte")->setImage($this->assets_url."20_black_world.png");
        }
    }
    
    public function overview_action() {
        $this->urls = ExternalDataURL::findBySeminar($_SESSION['SessionSeminar']);
    }

    public function details_action() {
        $this->url = new ExternalDataURL(array($_SESSION['SessionSeminar'], Request::get("url")));
        $this->url->fetch();
        Navigation::activateItem("/course/lagekarte/externedaten");
    }
    
    public function create_external_data_url_action() {
        if (!Request::isPost()) {
            throw new Exception("Nichtakzeptierte HTTP-Methode");
        }
        $url = new ExternalDataURL(array($_SESSION['SessionSeminar'], studip_utf8decode(Request::get("url"))));
        $url['name'] = studip_utf8decode(Request::get("name"));
        $url->store();
        PageLayout::postMessage(MessageBox::success(_("Externe Datenquelle wurde eingerichtet.")));
        $this->render_json(array(
            'success' => 1,
            'link' => PluginEngine::getURL($this->plugin, array('url' => Request::get("url")), "externedaten/details")
        ));
    }
    
}
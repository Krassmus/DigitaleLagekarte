<?php

require_once dirname(__file__)."/application.php";

class ExternedatenController extends ApplicationController {

    public function before_filter($action, $args)
    {
        parent::before_filter($action, $args);
        if ($GLOBALS['auth']->auth['devicePixelRatio'] > 1.2) {
            Navigation::getItem("/course/lagekarte")->setImage($this->plugin->getPluginURL()."/assets/40_black_world.png");
        } else {
            Navigation::getItem("/course/lagekarte")->setImage($this->plugin->getPluginURL()."/assets/20_black_world.png");
        }
    }

    public function overview_action()
    {
        $this->urls = ExternalDataURL::findBySeminar(Context::get()->id);
    }

    public function details_action()
    {
        $this->url = new ExternalDataURL(array(Context::get()->id, Request::get("url")));
        if (Request::isPost()) {
            if (Request::submitted("delete")) {
                $this->url->delete();
                PageLayout::postMessage(MessageBox::success(_("URL gelöscht.")));
                $this->redirect(PluginEngine::getURL($this->plugin, array('cid' => Context::get()->id), "externedaten/overview"));
            } else {
                $this->url['name'] = Request::get("name");
                $this->url['url'] = Request::get("new_url");
                $this->url['interval'] = Request::get("interval");
                $this->url['auth_user'] = Request::get("auth_user");
                $this->url['auth_pw'] = Request::get("auth_pw");
                $this->url->store();
                PageLayout::postMessage(MessageBox::success(_("Daten gespeichert.")));
                $this->redirect(PluginEngine::getURL($this->plugin, array('url' => Request::get("new_url"), 'cid' => Context::get()->id), "externedaten/details"));
            }
        }
        $this->url->fetch();
        Navigation::activateItem("/course/lagekarte/externedaten");
    }

    public function create_external_data_url_action()
    {
        if (!Request::isPost()) {
            throw new Exception("Nichtakzeptierte HTTP-Methode");
        }
        $url = new ExternalDataURL(array(Context::get()->id, Request::get("url")));
        $url['name'] = Request::get("name");
        $url->store();
        PageLayout::postMessage(MessageBox::success(_("Externe Datenquelle wurde eingerichtet.")));
        $this->render_json(array(
            'success' => 1,
            'link' => PluginEngine::getURL($this->plugin, array('url' => Request::get("url")), "externedaten/details")
        ));
    }

    public function toggle_external_data_url_activation_action()
    {
        if (!Request::isPost()) {
            throw new Exception("Nichtakzeptierte HTTP-Methode");
        }
        $output = array();
        $url = new ExternalDataURL(array(Context::get()->id, Request::get("url")));
        $url['active'] = Request::int("active");
        $url->store();
        $this->render_json($output);
    }

    public function mapping_window_action()
    {
        $output = array();
        $template = $this->get_template_factory()->open("externedaten/mapping_window.php");
        $url = new ExternalDataURL(array(Context::get()->id, Request::get('url')));
        $value = $url['last_object'];
        foreach (Request::getArray('path') as $path_index) {
            $value = $value[$path_index];
        }
        $template->set_attribute('value', $value);
        $template->set_attribute('url', $url);
        $template->set_attribute('pois', PointOfInterest::findCurrent(Context::get()->id));
        $output['html'] = $template->render();
        $this->render_json($output);
    }

    public function edit_mapping_action()
    {
        $output = array();
        $url = new ExternalDataURL(array(Context::get()->id, Request::get('url')));
        $mapping = $url['mapping'];
        if (Request::get('poi_id')) {
            $mapping[Request::get("path")] = array(
                'poi_id' => Request::option('poi_id'),
                'poi_attribute' => Request::get('poi_attribute')
            );
        } else {
            unset($mapping[Request::get("path")]);
        }
        $url['mapping'] = $mapping;
        $url->store();
        $url->apply_mapping();
        $this->render_json($output);
    }

}

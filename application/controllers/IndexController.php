<?php

class IndexController extends Zend_Controller_Action
{
    public function init() {
        /* Initialize action controller here */
    }
    public function indexAction() {       
    }
    public function factoryAction() {}
    public function vacanciesAction() {}
    public function historyAction() {}
    public function visionAction() {}
    public function sitemapAction() {
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        echo $this->view->navigation()->sitemap();
    }
}


<?php

class SitemapController extends Zend_Controller_Action
{
    public function init() {
        /* Initialize action controller here */
    }
    public function indexAction() {
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        echo $this->view->navigation()->sitemap()->setFormatOutput(true);
    }
    public function redirectAction()
    {
      $this->_redirect('/sitemap');
    }
}


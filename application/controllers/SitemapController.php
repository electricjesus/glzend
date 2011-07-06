<?php

class SitemapController extends Zend_Controller_Action
{
    public function init() {
        /* Initialize action controller here */
    }
    public function indexAction() {
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        //this is a bit hackish.. but it works though :l
        $config = new Zend_Config_Xml(APPLICATION_PATH . '/configs/navigation.xml', 'navigation');
        $navigation = new Zend_Navigation($config);
        
        echo $this->view->navigation($navigation)->sitemap()->setFormatOutput(false);
    }
    public function redirectAction()
    {
      $this->_redirect('/sitemap');
    }
}


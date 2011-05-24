<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initDoctype() {
        $this->bootstrap('layout');
        $layout = $this->getResource('layout');
        $view = $layout->getView();        
        $view->doctype('XHTML1_STRICT');        
        $view->headMeta()->appendHttpEquiv('Content-Type','text/html; charset=utf-8');
    }
    protected function _initNavigation() {
        $this->bootstrap('layout');
        $layout = $this->getResource('layout');
        $view = $layout->getView();
        $config = new Zend_Config_Xml(APPLICATION_PATH . '/configs/navigation.xml', 'navigation');
        $navigation = new Zend_Navigation($config);
        $view->navigation($navigation);
    }

}


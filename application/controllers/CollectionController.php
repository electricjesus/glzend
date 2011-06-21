<?php

class CollectionController extends Zend_Controller_Action
{
    public function init()
    {
        /* Initialize action controller here */        
    }
    public function indexAction() { 
        $this->_forward('latevictorian');
     }
    public function latevictorianAction() { /*action body*/ }
    public function artnouveauAction() { /*action body*/ }
    public function artdecoAction() { /*action body*/ }
    public function aAction() {
        $this->view->layout()->disableLayout();
        $this->view->assign('mode', $this->_getParam('m'));     // product or subcategory
        $this->view->assign('id', $this->_getParam('id'));      // the ID
        $this->view->assign('where', $this->_getParam('w'));    // carousel or slideshow
        $this->view->assign('category', $this->_getParam('c')); // category
    }
}


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
}


<?php

class IndexController extends Zend_Controller_Action
{    
    public function init()
    {
        /* Initialize action controller here */
        include_once "Databases/Products.php";
    }
    public function indexAction() {
        $_products = new Products;
        $_rows = $_products->fetchAll(
                    $_products
                        ->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                        ->setIntegrityCheck(false)
                        ->join('featured','featured.product_id = products.id')
                        ->where('featured.location = ?','homepage'));
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


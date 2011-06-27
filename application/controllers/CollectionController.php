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
    public function latevictorianAction() { 
        // construct data
        $this->_buildCategoryData('3', "late-victorian");
        $this->render('collection'); 
    }
    public function artnouveauAction() { 
        $this->_buildCategoryData('2', "art-nouveau");
        $this->render('collection');
    }
    public function artdecoAction() { 
        $this->_buildCategoryData('1',"art-deco");
        $this->render('collection');
    }
    public function aAction() {
        $this->view->layout()->disableLayout();
        $this->view->assign('mode', $this->_getParam('m'));     // product or subcategory
        $this->view->assign('id', $this->_getParam('id'));      // the ID
        $this->view->assign('where', $this->_getParam('w'));    // carousel or slideshow
        $this->view->assign('category', $this->_getParam('c')); // category
    }
    protected function _buildCategoryData ($_category_id, $_category_name) {
        $_products = new Databases_Products;
        $_subcategories = new Databases_Designers;
        //construct data for slideshow
        $_slideshowdata = 
            $_products->fetchAll(
                $_products
                ->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(false)
                ->join('featured','featured.product_id = products.id')
                ->where('featured.location = ?','category')->where('category_id = ?', $_category_id )
            );
        //construct data for carousel
        $_carouseldata = 
            $_products->fetchAll(
                $_products
                    ->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(false)
                    ->join('sort','sort.item_id = products.id')
                    ->where('sort.item_type = ?','product')->where('category_id = ?', $_category_id )
            );
        $_designerdata = $_subcategories->fetchAll(
                $_subcategories
                    ->select()->from(array('subcategories'),array('designerid'=>'id','name','category_id','description'))
                    ->setIntegrityCheck(false)->join('sort','sort.item_id = subcategories.id')
                    ->where('sort.item_type = ?','designer')->where('category_id = ?', $_category_id)
                    ->order('sort_order ASC')
                    );
        $this->view->assign('slideshowdata', $_slideshowdata);
        $this->view->assign('carouseldata', $_carouseldata);
        $this->view->assign('designerdata', $_designerdata);
        $this->view->assign('categoryname', $_category_name);
    }
}


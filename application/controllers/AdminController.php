<?php

class AdminController extends Zend_Controller_Action
{
    // public :
    public function init() {
    }
    public function indexAction() {
        //Zend_Auth -> hasIdentity():
            // proceed to admin function #1
        // else (no identity)
            // proceed to login action (redirect)
        $auth = Zend_Auth::getInstance();
        if(!($auth->hasIdentity())) {
            $this->_helper->redirector('login', 'admin');
        }
    }
    public function loginAction() {
        $form = new Application_Form_Login();
        $request = $this->getRequest();
        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {
                if ($this->_process($form->getValues())) {
                    // We're authenticated! Redirect to the admin home page
                    $this->_helper->redirector('index', 'admin');
                } else {
                    echo "error";
                }
            }
        }
        $this->view->form = $form;
    }
    public function logoutAction() {
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        Zend_Auth::getInstance()->clearIdentity();
        $this->_helper->redirector('login'); // back to login page
    }
    public function productsAction() {
    }
    public function usersAction () {    
    }
    // protected :
    protected function _process($values)
    {
        // Get our authentication adapter and check credentials
        $adapter = $this->_getAuthAdapter();
        $adapter->setIdentity($values['username']); 
        $adapter->setCredential($values['password']);

        $auth = Zend_Auth::getInstance();
        $result = $auth->authenticate($adapter);
        if ($result->isValid()) {
            $user = $adapter->getResultRowObject();
            $auth->getStorage()->write($user);
            return true;
        }
        return false;
    }
    protected function _getAuthAdapter() {
        
        $dbAdapter = Zend_Db_Table::getDefaultAdapter();
        $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
        
        $authAdapter->setTableName('users')
            ->setIdentityColumn('username')
            ->setCredentialColumn('password')
            ->setCredentialTreatment('SHA1(CONCAT(?,salt))');           
        
        return $authAdapter;
    }
}


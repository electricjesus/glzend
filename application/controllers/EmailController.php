<?php

class EmailController extends Zend_Controller_Action
{
    public function init()
    {
        /* Initialize action controller here */
    }
    
    public function indexAction()
    {
        // action body
    }
	
	public function sendMailAction()
	{
		$MailObj = new Zend_Mail();
		$emailMessage = $this->_request->getPost('comment');
		$fromEmail = $this->_request->getPost('email');
		$fromFullName = $this->_request->getPost('name');
		$to = "youngstownph@gmail.com";
		$subject = $this->_request->getPost('subject');
		
		$MailObj->setBodyText($emailMessage);
		$MailObj->setFrom($fromEmail, $fromFullName);
		$MailObj->addTo($to);
		$MailObj->setSubject($subject);
		
		
		try
			{
				$MailObj->send();
				echo "<p class=\"email-success\">Email sent successfully</p>";
			}
		catch(Zend_Mail_Exception $e)
			{
				echo $e;
			}
			
		$this->_helper->viewRenderer->setNoRender();
	}

}


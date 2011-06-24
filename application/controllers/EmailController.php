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
		$emailMessage = "Hey, this is Michael Christian Young";
		$fromEmail = "youngstownph@yahoo.com";
		$fromFullName = "<FROM_FULL_NAME>";
		$to = "youngstownph@gmail.com";
		$subject = "This is a sample";
		
		$MailObj->setBodyText($emailMessage);
		$MailObj->setFrom($fromEmail, $fromFullName);
		$MailObj->addTo($to);
		$MailObj->setSubject($subject);
		
		
		try
			{
				$MailObj->send();
				echo "Email sent successfully";
			}
		catch(Zend_Mail_Exception $e)
			{
				echo $e;
			}
			
		$this->_helper->viewRenderer->setNoRender();
	}
}


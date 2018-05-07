<?php
class Webkul_ChatSystem_chatloginController extends Mage_Core_Controller_Front_Action
{
        
    /**
     * @param void when user create account with chatsystem pannel then the account also created at magento account
     * @var array $error saves error if there is any error to create account
     * @var array $validationCustomer check for validation of customer login
     * @var array $validationResult holds validation error count
     * @var string $email sets the email of customer
     * @var string $password save customer login password  
    */
        
    public function userCreatePostAction()
    {
        $sendData = array();
        $errors = array();
        if (!$customer = Mage::registry('current_customer')) {
            $customer = Mage::getModel('customer/customer')->setId(null);
        }
        foreach (Mage::getConfig()->getFieldset('customer_account') as $code=>$node) {
            if ($node->is('create') && ($value = $this->getRequest()->getParam($code)) !== null) {
              $customer->setData($code, $value);
            }
        }
        if ($this->getRequest()->getParam('is_subscribed', false)) {
            $customer->setIsSubscribed(1);
        }
        try {
            $customer->setPasswordConfirmation($this->getRequest()->getParam('confirmation'));
            $validationCustomer = $customer->validate();
            if (is_array($validationCustomer)) {
              $errors=$validationCustomer;
            }   
            $validationResult = count($errors) == 0;
            if (true === $validationResult) {
                $customer->save();
                $this->_getSession()->setCustomerAsLoggedIn(Mage::getModel('customer/customer')->load($customer));
                $session = $this->_getSession();
                $customerName = $session->getCustomer()->getName();
                $sendData['loggedIn'] = 1;
                $sendData['customer'] = $customer->getEntityId();
                $sendData['customer_name'] = $customerName;
                $email = $session->getCustomer()->getEmail();
                $password = $session->getCustomer()->getPassword();
                $this->sendEmail($password);
            } else {
                $sendData['loggedIn'] = 0;
                $sendData['message'] = $errors[0];
                if ($sendData['message'] == "The last name cannot be empty.") {
                    $sendData['message'] = "Please Enter your full name.";
                }
            }
        } catch (Mage_Core_Exception $ex) {
            $sendData['loggedIn'] = 0;
            $sendData['message'] = $ex->getMessage();
        } catch (Exception $e) {
            $sendData['loggedIn'] = 0;
            $sendData['message'] = $e->getMessage();
        }
        $this->getResponse()->clearHeaders()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(json_encode($sendData));
    }
    
      /**
        * @param string $password send mail to customer to send
        *  password when user create and account at chatsystem
        *  @var string $email sets email of admin from backend
        *  @var string $name sets name of admin from backend
        *  @var string $template_variable sets the template values to send email
      */
     
        public function sendEmail($password)
        {
            $emailTemplate = Mage::getModel("core/email_template")->loadDefault("password_mail");
            $templateVariable = array();           
            $helper = Mage::helper('chatsystem');
            $emailTemplate->setSenderName($helper->getAdminEmail());
            $emailTemplate->setSenderEmail($helper->getAdminName());
            $customer = Mage::getModel('customer/session')->getCustomer(); 
            $email = $customer->getEmail();
            $name = $customer->getFirstname();          
            $templateVariable["customer_name"] = $customer->getFirstname()." ".$customer->getLastname();
            $templateVariable['link'] = "<a href=".Mage::getBaseurl().">Click Here to login!</a>";
            $templateVariable["msg"] = "You have succesfully created an account, and your password is: ".$password;
            $emailTemplate->getProcessedTemplate($templateVariable);
            $emailTemplate->send($email, $name, $templateVariable);
        }

        /**
         * @param void return customer session data
         */
     
        public function _getsession()
        {
            return Mage::getSingleton('customer/session');
        }
}
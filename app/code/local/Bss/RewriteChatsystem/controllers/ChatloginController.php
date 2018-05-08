<?php
require_once(Mage::getModuleDir('controllers','Webkul_ChatSystem').DS.'ChatloginController.php');

class Bss_RewriteChatsystem_ChatloginController extends Webkul_ChatSystem_ChatloginController{

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
                $this->_getSession()->setCustomerAsLoggedIn(Mage::getModel('customer/customer')->load($customer->getId()));
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

}
				
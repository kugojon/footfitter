<?php

class Webkul_ChatSystem_CustomerController extends Mage_Core_Controller_Front_Action
{

    /**
      * @param void user login to magento account with chatsystem pannel
      * @var array $data save data sent by the login form
      * @var string $email save email passed in form
      * @var string $password save password of login account passed by customer
      * @var object $session get object for session of customer
      * @var array $send_data store data for send to phtml for further process
     */
        
    public function userLoginPostAction()
    {
        Mage::app();
        $data = Mage::app()->getRequest()->getParams();
        $email = $data["username"];
        $password = $data["password"];
        if (count($data)>0 && $email!="" && $password!="") {
            $session = Mage::getSingleton('customer/session');
            try {
                $log = $session->login($email, $password);
                $session->setCustomerAsLoggedIn($session->getCustomer());
                $customerName = $session->getCustomer()->getName();
                $customerId = $session->getCustomerId();
                $sendData["loggedIn"] = 1;
                $sendData["message"] = "Login Success";
                $sendData["customer_id"] = $customerId;
                $sendData['customer_name'] = $customerName;
                $this->getcustomerStatus($customerId);
            }
            catch (Exception $ex) {
                $sendData["loggedIn"] = 0;
                $sendData["message"] = $ex->getMessage();
            }
        } else {
            $sendData["loggedIn"] = 0;
            $sendData["message"] = "Enter both Email and Password";
        }
        $this->getResponse()->clearHeaders()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(json_encode($sendData));
    }

    /**
      * @param int $customerId delete if customer status is true for chat system
      * @var int $customerId save customer id which is logged in before
      * @var object $customerCollection save collection from customer status table for $customerId
      * @var model $statusModel save model for particular wor of $customerCollection
     */
    
    public function getcustomerStatus($customerId)
    {
        $rowId = "";
        $customerCollection = Mage::getModel('chatsystem/customerstatus')->getCollection()
            ->addFieldToFilter('customer_id', array('eq'=>$customerId));
        foreach ($customerCollection as $customerStatus) {
            $rowId = $customerStatus->getId();
        }
        $statusModel = Mage::getModel("chatsystem/customerstatus")->load($rowId);
        $statusModel->delete();
    }
}
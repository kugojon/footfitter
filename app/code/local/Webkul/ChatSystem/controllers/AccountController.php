<?php
	require_once 'Mage/Customer/controllers/AccountController.php';
	class Webkul_ChatSystem_AccountController extends Mage_Customer_AccountController
	{
	    /**
         * @param void customer logout observer to update tables when customer logout
         * @var int $id sets row id for particular agent
         * @var int $agnetId sets agentid which is sets to customer in customer status table 
         * @var int $customerId sets customer id from customer session 
         * @var object $customerModel loads customer model for particular row id
         * @var object $countassignCollection sets collection from total assign tabe where agent id equals to $agentId
         * @var object $countassignModel load row from total assign where id equals to $id
         * @var int $finalCount finalcount stores the total count in total assign table for particular agent
         */
        
	    public function logoutAction(){
	    	$id = "";
	    	$agentId = "";
	       	$customerId = Mage::getSingleton('customer/session')->getCustomer()->getId(); 
	       	$customerCollection = Mage::getModel("chatsystem/customerstatus")->getCollection()
								->addFieldToFilter("customer_id",array("eq"=>$customerId));
	       	foreach ($customerCollection as $customer) {
				$id = $customer->getId();
				$agentId = $customer->getAgentId();
			}
			$customerModel = Mage::getModel("chatsystem/customerstatus")->load($id);
			$customerModel->delete();
			if($agentId != 0 && $agentId != ""){
				$countAssignCollection = Mage::getModel('chatsystem/totalassign')->getCollection()
									->addFieldToFilter('agent_id',array('eq'=>$agentId));
				foreach ($countAssignCollection as $assign) {
					$id = $assign->getId();
				}
				$countAssignModel = Mage::getModel('chatsystem/totalassign')->load($id);
				$finalCount = $countAssignModel->getTotal();
				$finalCount--;
				$countData = array('total'=>$finalCount);
				$countAssignModel = Mage::getModel('chatsystem/totalassign')->load($id)->addData($countData);
				$countAssignModel->setId($id)->save();
			}
			$this->_getSession()->logout()
	            ->renewSession();
	        $this->_redirect('*/*/logoutSuccess');
	    }
	}
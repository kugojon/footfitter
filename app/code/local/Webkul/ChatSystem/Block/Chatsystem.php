<?php
	class Webkul_ChatSystem_Block_Chatsystem extends Mage_Catalog_Block_Product_View_Abstract	{

		/**
         * @param int $customeId get whether agent assigned to a customer or not
         * @return int 1 or 0
         * @var int $customerId sets customer id for which checks status
         * @var object $customerCollection sets customer status collection where customer id is ezual to customerId
         */

		public function getCustomerStatus($customerId){
			$customerCollection = Mage::getModel("chatsystem/customerstatus")->getCollection()
								->addFieldToFilter("customer_id",array("eq"=>$customerId))
								->addFieldToFilter('agent_id',array('neq'=>0));
			if(count($customerCollection)){
				return 1;
			}
			else
				return 0;
		}


		/**
         * @param int $customerId get agent Id assigned to customer
         * @return int Return agent id
         * @var int $customerId sets customer id for which we are checking status
         * @var object $customer collection sets customer statis collection for customer id equal to $customerId
         * @var int $agentId sets agent id get from above customer statis collection
         */

		public function getAssignedAgentId($customerId){
			$customerCollection = Mage::getModel("chatsystem/customerstatus")->getCollection()
								->addFieldToFilter("customer_id",array("eq"=>$customerId));
			$agentId = "";
			foreach ($customerCollection as $customer) {
				$agentId = $customer->getAgentId();
			}
			if($agentId == 0)
				$agentId = "";
    	return $agentId;
    	}


    	/**
         * @param int $agentId get agent profile Data
         * @return array Return agent name and image
         * @var int $agentId sets agentid for which we are getting profile data
         * @var object $agentCollection sets collection of agent data where agent id equals to $agentId
         * @var array $agent sets aget array for each row of $agentCollection
         * @var array $agentData set agent profile data
         * @var image resize for resize agent profile image
         */

    	public function getAgentProfile($agentId){
    		$agentData = array();
    		$agentCollection=Mage::getModel("chatsystem/agent")->getCollection()
    						->addFieldToFilter('agent_id',array('eq'=>$agentId));
    		foreach ($agentCollection as $agent) {
    			$agentData['agent_name'] = $agent->getFirstName()." ".$agent->getLastName();
    			$agentData['about_agent'] = $agent->getAboutAgent();
    			if($agent->getImage()!="")
    			{
	    			$this->imageResize($agent->getImage(),'media','resize',40,40);
	    			$agentData['image'] = Mage::getBaseurl('media').DS.('resize/').$agent->getImage();
	    		}
	    		else
	    			$agentData['image'] = "";
    		}

    		return $agentData;
    	}


    	/**
		 * @var string $imgName contain image name
		 * @var string $path contain source path
		 * @var string $newPath contain new path for resized image
		 * @var int $width width for resizing image
		 * @var int $height height for resizing image
		 */

    	public function imageResize($imgName,$path,$newPath,$width,$height){
    		$base_path = Mage::getBaseDir($path).DS.$imgName;
            $new_path = Mage::getBaseDir("media").DS.$newPath.DS.$imgName;
            $imageObj = new Varien_Image($base_path);
            $imageObj->keepAspectRatio(true);
            $imageObj->backgroundColor(array(255,255,255));
            $imageObj->keepFrame(true);
            $imageObj->resize($height,$width);
            $imageObj->save($new_path);
    	}

		public function getStatus($customerId){
			$customerCollection = Mage::getModel("chatsystem/customerstatus")->getCollection()
								->addFieldToFilter("customer_id",array("eq"=>$customerId))
								->addFieldToFilter('agent_id',array('neq'=>0));
			if(count($customerCollection)){
				foreach ($customerCollection as $customerValue) {
					return $customerValue->getStatus();
				}
				// return 1;
			}
			else
				return 0;
		}
	}

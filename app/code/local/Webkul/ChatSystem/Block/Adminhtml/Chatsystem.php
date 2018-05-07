<?php
	class Webkul_ChatSystem_Block_Adminhtml_Chatsystem extends Mage_Catalog_Block_Product_View_Abstract	{

		/**
         * @param int $agentId get customer list assigned to an agent
         * @var object $agentAssignCollection sets customer status collection where agentId equal to $agentId
         * @var array $agentassign save data array for particular $agentAssignCollection row
         * @var int $agentId sets agent id
         * @return array Return customer id's array
         */

		public function getAssignedcustomer($agentId){
			$agentCustomerId = "";
			$agentAssignCollection = Mage::getModel('chatsystem/customerstatus')->getCollection()
									->addFieldToFilter("agent_id",array('eq'=>$agentId));
			foreach ($agentAssignCollection as $agentassign) {
				$agentCustomerId[] = $agentassign->getCustomerId();
			}
			return $agentCustomerId;
		}

		/**
         * @param int $agentId Check whether the particular admin is agent or not
         * @var array $agentData stores field from $agent data to send back to called function
         * @var array $agent store data in array for each particular row in $agentCollection
         * @var object $agentCollection gets agent collection where agent id equals to $agentId
         * @var int $agentId sets agent id
         * @return array Return agent data collection
         */

		public function isAgent($agentId){
    		$agentData = array();
    		$agentCollection = Mage::getModel("chatsystem/agent")->getCollection()
    						->addFieldToFilter('agent_id',array('eq'=>$agentId));
    		if(count($agentCollection)){
	    		foreach ($agentCollection as $agent) {
	    			$agentData['agent_name'] = $agent->getFirstName()." ".$agent->getLastName();
	    			$agentData['about_agent'] = $agent->getAboutAgent();
	    			if($agent->getImage()!="")	{
		    			$this->imageResize($agent->getImage(),'media','resize',40,40);
		    			$agentData['image']=Mage::getBaseurl('media').DS.('resize/').$agent->getImage();
		    		}
		    		else
		    			$agentData['image']=Mage::getBaseUrl('js').'chatsystem/images/pic-client.png';
	    		}return $agentData;
	    	}
		}

    	/**
         * @var imgname name of image save in data base
         * @var  $path path where the image exist
         * @var  $newPath path where to store image
         * @var  $width Width to resize the image size
         * @var  $height height to resize height of image
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

		/**
	     * [get status of agent]
	     * @param  [int] $agentId [id of agent]
	     * @return [int]          [status of agent]
	     */
	    public function getAgentStatus($agentId) {
	        $agentAssignCollection = Mage::getModel('chatsystem/agent')->getCollection()
	                                ->addFieldToFilter('agent_id',array('eq'=>$agentId))
	                                ->getFirstItem();

	        return $agentAssignCollection->getStatus();
	    }

	}

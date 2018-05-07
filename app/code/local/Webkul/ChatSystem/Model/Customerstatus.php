<?php
	class Webkul_ChatSystem_Model_Customerstatus extends Mage_Core_Model_Abstract	{

		/**
		 * @param void construct customer status model for chat system
		 */


    	public function _construct(){
	        parent::_construct();
	        $this->_init("chatsystem/customerstatus");
    	}

    }
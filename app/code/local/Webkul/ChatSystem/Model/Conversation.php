<?php
	class Webkul_ChatSystem_Model_Conversation extends Mage_Core_Model_Abstract	{


		/**
		 * @param void construct conversation model
		 */

    	public function _construct()    {
	        parent::_construct();
	        $this->_init("chatsystem/conversation");
    	}

    }
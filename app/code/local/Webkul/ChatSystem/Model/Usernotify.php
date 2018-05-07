<?php
	class Webkul_ChatSystem_Model_Usernotify extends Mage_Core_Model_Abstract	{

		/**
		 * @param void construct table for user notification in chat system
		 */


    	public function _construct()    {
	        parent::_construct();
	        $this->_init("chatsystem/usernotify");
    	}

    }
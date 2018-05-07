<?php
	class Webkul_ChatSystem_Model_Adminnotify extends Mage_Core_Model_Abstract	{


		/**
		 * @param void create adminnotify model
		 */

    	public function _construct()    {
	        parent::_construct();
	        $this->_init("chatsystem/adminnotify");
    	}

    }
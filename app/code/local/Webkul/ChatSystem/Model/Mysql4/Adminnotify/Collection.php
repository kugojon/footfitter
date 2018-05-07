<?php
	class Webkul_ChatSystem_Model_Mysql4_Adminnotify_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract	{
		
		/**
		 * @param void use to define collection for admin notification table
		 */


    	public function _construct()	{
		    parent::_construct();
		    $this->_init("chatsystem/adminnotify");
    	}

	}
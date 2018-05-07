<?php
	class Webkul_ChatSystem_Model_Mysql4_Adminnotify extends Mage_Core_Model_Mysql4_Abstract		{

		/**
		 * @param void sets column to load data
		 */

	    public function _construct()	{    
	        $this->_init("chatsystem/adminnotify","id");
	    }
	    
	}
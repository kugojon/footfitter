<?php
	class Webkul_ChatSystem_Model_Mysql4_Customerstatus extends Mage_Core_Model_Mysql4_Abstract		{

		/**
		 * @param void sets column to load data from table customer status
		 */


	    public function _construct()	{    
	        $this->_init("chatsystem/customerstatus","id");
	    }
	    
	}
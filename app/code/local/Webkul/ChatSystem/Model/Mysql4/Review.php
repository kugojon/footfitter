<?php
	class Webkul_ChatSystem_Model_Mysql4_Review extends Mage_Core_Model_Mysql4_Abstract		{

		/**
		 * @param void sets column to load data from table
		 */


	    public function _construct()	{    
	        $this->_init("chatsystem/review","id");
	    }
	    
	}
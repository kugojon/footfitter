<?php
	class Webkul_ChatSystem_Model_Mysql4_Totalassign extends Mage_Core_Model_Mysql4_Abstract		{

		/**
		 * @param void sets column to load data from data base
		 */


	    public function _construct()	{    
	        $this->_init("chatsystem/totalassign","id");
	    }
	    
	}
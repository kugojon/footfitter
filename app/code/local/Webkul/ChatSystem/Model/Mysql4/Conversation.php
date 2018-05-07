<?php
	class Webkul_ChatSystem_Model_Mysql4_Conversation extends Mage_Core_Model_Mysql4_Abstract		{


		/**
		 * @param void sets columnt o load data for conversation
		 */

	    public function _construct()	{    
	        $this->_init("chatsystem/conversation","id");
	    }
	    
	}
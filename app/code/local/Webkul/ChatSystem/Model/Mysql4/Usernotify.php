<?php
	class Webkul_ChatSystem_Model_Mysql4_Usernotify extends Mage_Core_Model_Mysql4_Abstract		{

		/**
		 * @param void sets column nae to load data from usernotification tabel
		 */

	    public function _construct()	{    
	        $this->_init("chatsystem/usernotify","id");
	    }
	    
	}
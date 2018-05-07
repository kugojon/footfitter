<?php
	class Webkul_ChatSystem_Model_Mysql4_Customerstatus_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract	{
		
		/**
		 * @param void use to set collection for customer status table
		 */

    	public function _construct()	{
		    parent::_construct();
		    $this->_init("chatsystem/customerstatus");
    	}

	}
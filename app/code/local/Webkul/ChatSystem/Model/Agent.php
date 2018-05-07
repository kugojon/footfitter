<?php
	class Webkul_ChatSystem_Model_Agent extends Mage_Core_Model_Abstract	{


		/**
		 * @param void construct agent model
		 */

    	public function _construct()    {
	        parent::_construct();
	        $this->_init("chatsystem/agent");
    	}

    }
<?php
	class Webkul_ChatSystem_Model_Totalassign extends Mage_Core_Model_Abstract	{

		/**
		 * @param void construct model for total assign table
		 */


    	public function _construct()    {
	        parent::_construct();
	        $this->_init("chatsystem/totalassign");
    	}

    }
<?php
	class Webkul_ChatSystem_Model_Review extends Mage_Core_Model_Abstract	{


		/**
		 * @param void constuct model for review
		 */

    	public function _construct()    {
	        parent::_construct();
	        $this->_init("chatsystem/review");
    	}

    }
<?php
	class Webkul_ChatSystem_Block_Adminhtml_Chats extends Mage_Adminhtml_Block_Widget_Grid_Container {

		/**
         * @param void Construct block header for Chats grid
         */

	    public function __construct() {
	        $this->_controller = "adminhtml_chats";
	        $this->_blockGroup = "chatsystem";
	        $this->_headerText = Mage::helper("chatsystem")->__('View Chat History');
	        parent::__construct();
	        $this->removeButton("add");
	    }

	}
<?php
	class Webkul_ChatSystem_Block_Adminhtml_Individualchat extends Mage_Adminhtml_Block_Widget_Grid_Container {


		/**
         * @param void Construct block header for Individual Chats grid
         */

	    public function __construct() {
	        $this->_controller = "adminhtml_individualchat";
	        $this->_blockGroup = "chatsystem";
	        $this->_headerText = Mage::helper("chatsystem")->__("Individual Chat History");
	        parent::__construct();
	        $this->removeButton("add");
	    }

	}
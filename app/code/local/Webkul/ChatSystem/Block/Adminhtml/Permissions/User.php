<?php
	class Webkul_ChatSystem_Block_Adminhtml_Permissions_User extends Mage_Adminhtml_Block_Widget_Grid_Container {


		/**
         * @param void Construct block header for Agent grid
         */

	    public function __construct() {
	        $this->_controller = "adminhtml_permissions_user";
	        $this->_blockGroup = "chatsystem";
	        $this->_headerText = Mage::helper("chatsystem")->__('Agent Details');
	        parent::__construct();
	        $this->removeButton("add");
	    }

	}
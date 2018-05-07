<?php
	class Webkul_ChatSystem_Block_Adminhtml_Review extends Mage_Adminhtml_Block_Widget_Grid_Container {


		/**
         * @param void Construct block header for Review and comments grid
         */

	    public function __construct() {
	        $this->_controller = "adminhtml_review";
	        $this->_blockGroup = "chatsystem";
	        $this->_headerText = Mage::helper("chatsystem")->__("Review and Comments");
	        parent::__construct();
	        $this->removeButton("add");
	    }

	}
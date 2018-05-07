<?php
    include_once("Mage/Adminhtml/controllers/IndexController.php");
    class Webkul_ChatSystem_Adminhtml_ChatsystemController extends Mage_Adminhtml_IndexController {

    	/**
         * @param void action call at grid call
         */

        public function indexAction() {
            $this->_initAction()->renderLayout();
        }

        /**
         * @param void layout grid at admin pannel with agent details
         */

        protected function _initAction() {
            $this->loadLayout()->_setActiveMenu("chatsystem");
            $this->getLayout()->getBlock("head")->setTitle($this->__("Agent Details"));
            return $this;
        }


        /**
         * @param void call when apply filter on grid by ajax
         */


        public function gridAction(){
            $this->loadLayout();
            $this->getResponse()->setBody($this->getLayout()->createBlock("chatsystem/adminhtml_permissions_user_grid")->toHtml());
        }
    }

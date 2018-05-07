<?php
    include_once("Mage/Adminhtml/controllers/IndexController.php");
    class Webkul_ChatSystem_Adminhtml_ReviewController extends Mage_Adminhtml_IndexController {

        /**
         * @param void Call a grid which shows reviews of agent
        */
       
        public function indexAction() {
            $this->_initAction()->renderLayout();
        }

        protected function _initAction() {
            $this->loadLayout()->_setActiveMenu("chatsystem");
            $this->getLayout()->getBlock("head")->setTitle($this->__("Review and Comments"));
            return $this;
        }
        
        /**
         * @param void call when ajax filter is applied in grid
         */
        
        public function gridAction(){
            $this->loadLayout();
            $this->getResponse()->setBody($this->getLayout()->createBlock("chatsystem/adminhtml_review_grid")->toHtml());
        }
    }
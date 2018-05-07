<?php
    include_once("Mage/Adminhtml/controllers/IndexController.php");
    /**
     * [Webkul_ChatSystem_Adminhtml_SettingsController description]
     */
    class Webkul_ChatSystem_Adminhtml_SettingsController extends Mage_Adminhtml_Controller_Action
    {
        /**
         * [startseverAction description]
         * @return [type] [description]
         */
        public function startSeverAction()
        {
            $data = $this->getRequest()->getParams();
            $response = array();
            $appRoot= Mage::getRoot();
            $rootPath   = dirname($appRoot);
            $node = system('whereis node');
            $nodePath = explode(' ', $node);
            if (!isset($nodePath[1]) || $nodePath[1] == '') {
                $node = system('whereis nodejs');
                $nodePath = explode(' ', $node);
            }
            if (count($nodePath)) {
                if (substr(php_uname(), 0, 7) == "Windows") {
                    pclose(popen("start /B ". $nodePath[1].' '.$rootPath.'/server.js', "r"));
                } else {
                    system($nodePath[1].' '.$rootPath.'/server.js' . " > /dev/null &");
                }
                $response = ('Server Running.');
                // set server status
                Mage::getModel('core/config')->saveConfig('chatsystem/chatsystem/serverstatus', "start");

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('chatsystem')->__('Server Started'));
            } else {
                $response = __('Nodejs Path not found.');
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('chatsystem')->__('Nodejs path not found'));
            }
            Mage::app()->getResponse()->setBody(json_encode($response));
        }

        public function endSeverAction()
        {
            $data = $this->getRequest()->getParams();
            $response = array();

            $getUserPath = exec('whereis fuser');
            if ($getUserPath) {
                $getUserPath = explode(' ', $getUserPath);
                if (isset($getUserPath[1])) {
                    $stopServer = exec($getUserPath[1].' -k '.$data['port'].'/tcp');
                    $response = ('Server Stopped.');
                    // server status
                    Mage::getModel('core/config')->saveConfig('chatsystem/chatsystem/serverstatus', "stop");

                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('chatsystem')->__('Server has been stopped.'));
                }
            } else {
                $response = __('Something went wrong.');
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('chatsystem')->__('Something went wrong.'));
            }
            Mage::app()->getResponse()->setBody(json_encode($response));
        }
    }

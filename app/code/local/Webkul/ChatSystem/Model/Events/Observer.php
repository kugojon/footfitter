<?php
    class Webkul_ChatSystem_Model_Events_Observer {

        /**
         * @param object $observer observer after admin is logged in to save agent status
         * @var object $admindetail sets logged in admin session
         * @var object $adminModel sets agent model
         * @var object $adminCollection sets current loggedin agent collection
         * @var array $agent sets array for particular row
         * @var int $id get row id for particular $agent
         * @var array $data sets data to update table
         * @var object $agentData sets agent model for particular $id
         * @var object $agentAssignCollection get collection for particular agent id from total assign table
         * @var object $agentAssignModel gets model for total assign and create row
         */

        public function adminloggedin($observer) {
            $adminDetail = $observer->getEvent()->getUser();
            $adminModel = Mage::getModel("chatsystem/agent");
            $adminCollection = $adminModel->getCollection()
                            ->addfieldToFilter('agent_id',array('eq'=>$adminDetail->getUserId()));
            
            if(count($adminCollection)) {
                foreach ($adminCollection as $agent) {
                    $id = $agent->getId();
                }
                $data = array('status'=>1);
                $agentData = Mage::getModel("chatsystem/agent")->load($id)->addData($data);
                $agentData->setId($id)->save();
                $agentAssignCollection = Mage::getModel('chatsystem/totalassign')->getCollection()
                                        ->addFieldToFilter('agent_id',array('eq'=>$adminDetail->getUserId()));
                if(count($agentAssignCollection) == 0) {
                    $agentAssignModel = Mage::getModel('chatsystem/totalassign');
                    $agentAssignModel->setAgentId($adminDetail->getUserId());
                    $agentAssignModel->setTotal(0);
                    $agentAssignModel->save();
                }
            }
        }

        /**
         * @param object $observer observer at new admin user save to save agent in chatsystem table
         * @var array $data sets data for store image
         * @var object $agent get admin data for observer
         * @var object $agentCollection  for particular admin id from agent table
         * @var int $id gets agent row id
         * @var object $agentModel loads modle for particular id
         * @var object $uploader sets object for file uploader
         */
        
        public function adminUserSave($observer){
            $data=array();
            $agent=$observer->getDataObject();
            $agentCollection=Mage::getModel('chatsystem/agent')->getCollection()->addFieldToFilter('agent_id',array('eq'=>$agent->getUserId()));
            if(count($agentCollection))
                {
                    foreach ($agentCollection as $value) {
                        $id=$value->getId();
                    }
                    $agentModel = Mage::getModel("chatsystem/agent")->load($id);
                    if($agent->getAgentType()!=2)
                    {
                        $agentModel->delete();
                    }
                }
            else
                $agentModel = Mage::getModel("chatsystem/agent");
            if($agent->getAgentType()==2)
            { 
                if (!empty($_FILES["image"]["name"])) {
                    try {
                       
                        $ext = substr($_FILES["image"]["name"], strrpos($_FILES["image"]["name"], ".") + 1);
                        $fname = "File-" . time() . "." . $ext;
                        $uploader = new Varien_File_Uploader("image");
                        $uploader->setAllowedExtensions(array("jpg", "jpeg", "gif", "png"));
                        $uploader->setAllowRenameFiles(true);
                        $uploader->setFilesDispersion(false);
                        $path = Mage::getBaseDir("media").DS."agent";
                        $uploader->save($path, $fname);
                        $imagedata["image"] = "agent/".$fname;
                    }
                    catch (Exception $e) {
                        Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
                        $this->_redirect("*/*/edit", array("id" => $agent("id")));
                        return;
                    }
                }
                if (!empty($imagedata["image"]))
                    $data["image"] = $imagedata["image"];
                else {
                    if(isset($data["image"]["delete"]) && $data["image"]["delete"] == 1) {
                        if($data["image"]["value"] != "") {
                            $this->removeFile(Mage::getBaseDir("media").DS.$data["image"]["value"]);
                        }
                        $data["image"] = "";
                    }
                    else
                        unset($data["image"]);
                }
                $agentModel->setAgentId($agent->getUserId());
                $agentModel->setFirstName($agent->getFirstname());
                $agentModel->setLastName($agent->getLastname());
                $agentModel->setEmail($agent->getEmail());
                $agentModel->setUsername($agent->getUsername());
                $agentModel->setAboutAgent($agent->getAboutAgent());
                $agentModel->setImage($data['image']);
                $agentModel->save();
            }
        }

        /**
         * @param object $observer update table when admin user is deleted
         * @var int $agentId get agent if from observer
         * @var object $agentCollection sets collection for particular agent where id equal to $agentId
         * @var int $id sets row id
         * @var object $agentModel loads and sets data for particular model
         */
        
        public function adminUserDelete($observer){
            $agentId=$observer->getDataObject()->getUserId();
            $agentCollection=Mage::getModel('chatsystem/agent')->getCollection()->addFieldToFilter('agent_id',array('eq'=>$agentId));
            foreach ($agentCollection as $agent) {
                $id=$agent->getId();
            }
            $agentModel=Mage::getModel("chatsystem/agent")->load($id);
            $agentModel->delete();
        }

    }
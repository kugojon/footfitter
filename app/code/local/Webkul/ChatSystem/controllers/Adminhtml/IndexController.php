<?php
include_once("Mage/Adminhtml/controllers/IndexController.php");
class Webkul_ChatSystem_Adminhtml_IndexController extends Mage_Adminhtml_IndexController
{

    /**
     * @param void Gives Collection of admin session
     * @return int Return admin session admin id
     * @var object $adminSession stores current login admin session
     */

    public function adminSession()
    {
        $adminSession = Mage::getSingleton('admin/session');
        return $adminSession->getUser()->getUserId();
    }

     /**
     * @param void this call a page to start chat
     * @var object $agentCollection gets agent collection where agent id equals to $agentId
     * @var int $agentId sets agent id
     * @return int Return admin session admin id
     */

    public function isAgent($agentId)
    {
        $agentCollection = Mage::getModel('chatsystem/agent')->getCollection()
                        ->addFieldToFilter('agent_id', array('eq'=>$agentId));
        if (count($agentCollection)) {
            return true;
        } else {
            return false;
        }
    }

     /**
     * @param void It displays the chat window if admin type is agent
     * @var int $agentId sets agent id
     */

    public function startChatAction()
    {
        $agentId=$this->adminSession();
        if ($this->isAgent($agentId)) {
            $this->loadLayout();
            $this->renderLayout();
        } else {
            $this->_redirect('adminhtml/dashboard/index');
        }
    }
     /**
     * @param void use to get current logged inagent
     * @var object $agentCollection gets agent collection where agent id equals to $agentId
     * @var int $agentId sets agent id from admin session
     * @var array $agent store data in array for each particular row in $agentCollection
     * @var array $agentData stores field from $agent data to send back to called function
     * @var object $agentCollection stores collection of agent where agentId equals to $agentId
     * @return array Return agent profile data
     */

    public function getAgentData()
    {
        $agentId = $this->adminSession();
        $agentCollection = Mage::getModel('chatsystem/agent')->getCollection()
            ->addFieldToFilter('agent_id', array('eq'=>$agentId));
        $agentData = array();
        foreach ($agentCollection as $agent) {
            $agentData['name'] = $agent->getFirstName()." ".$agent->getLastName();
            if ($agent->getImage()!="") {
                $this->imageResize($agent->getImage(), 'media', 'resize', 100, 100);
                $agentData['image'] = Mage::getBaseurl('media').DS.('resize/').$agent->getImage();
            } else {
                $agentData['image'] = Mage::getBaseUrl('js').'chatsystem/images/pic-client.png';
            }
        }
        return $agentData;
    }


    /**
     * @var imgname name of image save in data base
     * @var  $path path where the image exist
     * @var  $newPath path where to store image
     * @var  $width Width to resize the image size
     * @var  $height height to resize height of image
     */

    public function imageResize($imgName, $path, $newPath, $width, $height)
    {
        $basePath = Mage::getBaseDir($path).DS.$imgName;
        $newPath = Mage::getBaseDir("media").DS.$newPath.DS.$imgName;
        $imageObj = new Varien_Image($basePath);
        $imageObj->keepAspectRatio(true);
        $imageObj->backgroundColor(array(255,255,255));
        $imageObj->keepFrame(true);
        $imageObj->resize($height, $width);
        $imageObj->save($newPath);
    }


    /**
     * @param void customer which are assigned and have chated with agent
     * @return object Return customer id with there total conversation
     * @var int $agentId sets agent id from admin session
     * @var string $content store multiple customer id with their respective total no. of conversations
     * @var array $contentArray use to store $id as index and $conversationCount as value
     * @var int $conversationCount is total no of conversation of particular id
     * @var object $conversationCollection stores conversation collection where for id=$id and $agentId=$agentId
     */

    public function customerIdWithConversationCountAction()
    {
        $content = "";
        $contentArray = "";
        $agentId = $this->adminSession();
        $customerCollection = Mage::getModel('customer/customer')
                              ->getCollection();
        foreach ($customerCollection as $key => $customer) {
            $id = $customer->getEntityId();
            $conversationCollection = Mage::getModel("chatsystem/conversation")->getCollection()
                                    ->addFieldToFilter('forid', array('eq'=>$id))
                                    ->addFieldToFilter('agent_id', array('eq'=>$agentId));
            $conversationCount = count($conversationCollection)+10;
            $content.=$id."-".$conversationCount."~";
            $contentArray[$id] = $conversationCount;
        }
        $this->getResponse()->clearHeaders()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(json_encode($contentArray));
    }


    protected function _initAction()
    {
        $this->loadLayout()->_setActiveMenu("chatsystem");
        $this->getLayout()->getBlock("head")->setTitle($this->__("Chat History"));
        return $this;
    }

    /**
     * @param void call to grid at admin pannel
     */

    public function indexAction()
    {
        $this->_initAction()->renderLayout();
    }

     /**
     * @param void call when ajax filter applied on grid
     */


    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody($this->getLayout()->createBlock("chatsystem/adminhtml_chats_grid")->toHtml());
    }


    /**
     * @param void return no of conversation which are not read by admin
     * @var object $conversationCollection stores conversation collection where for id=$id and $agentId=$agentId
     * @var array $paramData store data of request
     * @var int $conversationCount store number of conversation
     * @var object $adminNotifyCollection stores collection of admin notification
     * where userid =$paramData["id"], agent_id=$paramData['agent_id'] and status =1
     * @var int $notificationCount counts number of notifications
     * @var string $noOfRow stores formated data as $conversationCount."-".$notificationCount to send
     * @return string return total count and notification count
     *
     */

    public function noOfRowsAction()
    {
        $paramData = $this->getRequest()->getParams();
        $conversationCollection = Mage::getModel("chatsystem/conversation")->getCollection()
            ->addFieldToFilter('forid', array('eq'=>$paramData["id"]))
            ->addFieldToFilter('agent_id', array('eq'=>$paramData['agent_id']));
        $conversationCount = count($conversationCollection)+10;
        $adminNotifyCollection = Mage::getModel("chatsystem/adminnotify")->getCollection()
            ->addFieldToFilter("userid", $paramData["id"])
            ->addFieldToFilter('agent_id', array('eq'=>$paramData['agent_id']))
            ->addFieldToFilter("status", 1);
        $notificationCount = count($adminNotifyCollection);
        $noOfRow = $conversationCount."-".$notificationCount;
        $this->getResponse()->clearHeaders()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody($noOfRow);
    }

     /**
     * @param void call to unset user session data
     *  @var int $agentId sets agent id from admin session
     * @var object $customerCollection sets customer status collection
     * where customer id is equal to $customerId and agentId equal to $agentId
     * @var array $customer save data array for particular customerCollection row
     * @var int $rowid store row id of $customer array
     * @var obejct countAssignCollection save collection of total assign table where agent id is equals to $agentId
     * @var array $assign save data of particular row data $ $countAssignCollection
     * @var object $customerStatusModel it save the model for a customer with id equals to $id
     * @var int $finalCount stores total field of $assign array
     * @var array $countData store indexs with value to be updated in countAssignModel
     * @var int $id stores customer id
     */

    public function userSessionUnsetAction()
    {
        $rowId = "";
        $agentId = $this->adminSession();
        $id = $this->getRequest()->getParam("uid");
        Mage::getModel('admin/session')->unsetData($id);
        $customerCollection = Mage::getModel("chatsystem/customerstatus")->getCollection()
                            ->addFieldToFilter("customer_id", array("eq"=>$id))
                            ->addFieldToFilter("agent_id", array('eq'=>$agentId));
        foreach ($customerCollection as $customer) {
            $rowId = $customer->getId();
        }
        $customerStatusModel = Mage::getModel('chatsystem/customerstatus')->load($rowId);
        $customerStatusModel->delete();
        $countAssignCollection = Mage::getModel('chatsystem/totalassign')->getCollection()
                            ->addFieldToFilter('agent_id', array('eq'=>$agentId));
        if (count($countAssignCollection)) {
            foreach ($countAssignCollection as $assign) {
                $agentRowid = $assign->getId();
            }
            $countAssignModel = Mage::getModel('chatsystem/totalassign')->load($agentRowid);
            $finalCount = $countAssignModel->getTotal();
            if ($finalCount == 0)
                $finalCount = 0;
            else
            $finalCount--;
            $countData = array('total'=>$finalCount);
            $countAssignModel = Mage::getModel('chatsystem/totalassign')->load($agentRowid)->addData($countData);
            $countAssignModel->setId($agentRowid)->save();
        }
    }

     /**
     * @param void call to set customer session data
     * @var int $id save customer id
     */

    public function userSessionAction()
    {
        $id=$this->getRequest()->getParam("uid");
        Mage::getModel('admin/session')->setData($id, $id);
    }

    /**
     * @param void logout agent when click on logout button
     * @var object $agentCollection gets agent collection where agent id equals to $agentId and status equals to 1
     * @var int $agentId sets agent id from admin session
     * @var array $agent save data of particular row of $agentCollection
     * @var array $customer save data array for particular customerCollection row
     * @var object $agentModel set to model for $agentAssignid for total assign table
     * @var object $customerCollection sets customer status collection where agentId equal to $agentId
     */

    public function logoutAction()
    {
        $adminSession = Mage::getSingleton('admin/session');
        $agentAssignId="";
        if ($adminSession->getUser()) {

            $agentId= $adminSession->getUser()->getUserId();
            $agentCollection=Mage::getModel('chatsystem/agent')->getCollection()
                                ->addFieldToFilter('agent_id', array('eq'=>$agentId))
                                ->addFieldToFilter('status', array('eq'=>1));
            if (count($agentCollection)) {
                foreach ($agentCollection as $agent) {
                    $id=$agent->getId();
                }
                $data=array('status'=>0);
                $agentModel=Mage::getModel('chatsystem/agent')->load($id)->addData($data);
                $agentModel->setId($id)->save();
                $assignCollection=Mage::getModel('chatsystem/totalassign')->getCollection()
                                    ->addFieldToFilter('agent_id', array('eq'=>$agentId));
                foreach ($assignCollection as $agentassign) {
                    $agentAssignId=$agentassign->getId();
                }
                $assignModel=Mage::getModel('chatsystem/totalassign')->load($agentAssignId);
                $assignModel->delete();
                $customerCollection=Mage::getModel("chatsystem/customerstatus")->getCollection()
                            ->addFieldToFilter("agent_id", array("eq"=>$agentId));
                if (count($customerCollection > 0)) {
                    foreach ($customerCollection as $customer) {
                        $rowId=$customer->getId();
                        $model=Mage::getModel("chatsystem/customerstatus")->load($rowId)->delete();
                    }
                }
            }
            $adminSession->unsetAll();
            $adminSession->getCookie()->delete($adminSession->getSessionName());
            $adminSession->addSuccess(Mage::helper('adminhtml')->__('You have logged out.'));
            Mage::dispatchEvent(
                'admin_session_user_logout',
                array('adminsession'=>Mage::getSingleton('admin/session'))
            );
            $this->_redirect('*');
        }
        $this->_redirect('*');
    }

    /**
     * @param void check for agent either a new message or not
     * @return srting return customer name with their id
     * @var string $arrayName store all customer name with userid
     * @var object $customer loads model for customer table where id equals to $notification->getUserid()
     * @var object adminNotoficationCollection stores collection of
     * adminnotification table where agentid equals to $paramData['agent_id'] and status equals to 1
     */

    public function checkIfNewMessageThereAction()
    {
        $arrayName = "";
        $paramData = $this->getRequest()->getParams();
        $adminNotoficationCollection = Mage::getModel("chatsystem/adminnotify")->getCollection()
                                    ->addFieldToFilter("agent_id", array('eq'=>$paramData['agent_id']))
                                    ->addFieldToFilter("status", 1);
        foreach ($adminNotoficationCollection as $notification) {
            $customer = Mage::getModel("customer/customer")->load($notification->getUserid());
            $arrayName .= $customer->getName()."-";
            $arrayName .= $notification->getUserid().",";
        }
        $this->getResponse()->clearHeaders()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody($arrayName);
    }

    /**
     * @param void change status when message is read by agent
     * @var int $agentId sets agent id from admin session
     * @var object adminNotoficationCollection stores collection of
     * adminnotification table where agentid equals to $agentId and userid equals to $paramData['userid']
     * @var array $notification stores data for particular row of $adminNotificationCollection
     * @var int $id stores row id
     * @var object $model set model of admin notify with id equals to $id
     */

    public function notifyMsgIsReadnowAction()
    {
        $agentId = $this->adminSession();
        $adminNotificationCollection = Mage::getModel("chatsystem/adminnotify")->getCollection()
                            ->addFieldToFilter("userid", $this->getRequest()->getParam("userid"))
                            ->addFieldToFilter("agent_id", array('eq'=>$agentId));
        foreach ($adminNotificationCollection as $notification) {
            $id = $notification->getId();
        }
        $model = Mage::getModel("chatsystem/adminnotify")->load($id);
        $model->setStatus(0);
        $model->save();
    }


    /**
     * @var array $paramData stores the data which is send by the request i.e. by the form
     * @param void save message sent by agent to customer
     * @var object $conversationModel sets to model of conversation
     * @var array $notification stores data for particular row of $userNotifyCollection
     * @var object $userNotifyCollection sets to collection of user
     * notofication where user_id equals to $paramData["forid"] and agent_id euals to $paramData['agent_id']
     *
     */

    public function saveConversationAction()
    {
        $id = "";
        $paramData = $this->getRequest()->getParams();
        // check if image
        if ($paramData['file_name']) {
            $imageData = $paramData["message"];
            $filteredData=substr($imageData, strpos($imageData, ",")+1);
            $unencodedData=base64_decode($filteredData);

            $file = new Varien_Io_File();
            $path = Mage::getBaseDir()."/media/chatsystem/attachment/";
            if (!is_dir($path))
                $file->mkdir($path);
            $file_name = $paramData['file_name'];
            $fp = fopen( $path.$file_name, 'wb' );
            fwrite( $fp, $unencodedData);
            fclose( $fp );
        }

        $conversationModel = Mage::getModel("chatsystem/conversation");
        $conversationModel->setFromname($paramData['agent_name']);
        $conversationModel->setForid($paramData["forid"]);
        $conversationModel->setToname($paramData["toname"]);

        if ($paramData['file_name']) {
            $conversationModel->setMessage("#type#".$paramData["file_type"].$file_name);
        } else {
            $conversationModel->setMessage($paramData["message"]);
        }
        $conversationModel->setAgentId($paramData['agent_id']);
        $conversationModel->setAgentName($paramData['agent_name']);
        $conversationModel->setToType('Customer');
        $conversationModel->setCreatedAt(time());
        $conversationModel->save();
        $userNotifyCollection = Mage::getModel("chatsystem/usernotify")->getCollection()
                            ->addFieldToFilter("userid", $paramData["forid"])
                            ->addFieldToFilter("agent_id", $paramData['agent_id']);
        foreach ($userNotifyCollection as $notification) {
            $id = $notification->getId();
        }
        if ($id > 0) {
            $model = Mage::getModel("chatsystem/usernotify")->load($id);
            $model->setUserid($paramData["forid"]);
            $model->setAgentId($paramData['agent_id']);
            $model->setStatus(1);
            $model->save();
        } else {
            $model = Mage::getModel("chatsystem/usernotify");
            $model->setUserid($paramData["forid"]);
            $model->setAgentId($paramData['agent_id']);
            $model->setStatus(1);
            $model->save();
        }
        $customerId=$paramData["forid"];
        Mage::getModel('admin/session')->setData($customerId, $customerId);
    }


    /**
     * @param void fetch messages for admin sent by users
     * @return array conversation with the customer's name,message and time
     * @var object $conversationCollection stores collection of
     * conversation table where forid equals to $paramData['id'], agent_id equals to $agentId
     * @var int $agentId sets agent id from admin session
     * @var array $fullconversation stores the full conversation data in json format
     * @var array $isMsghttp stores string in array after expload by http://
     * @var array $isMsghttp stores string in array after expload by http://
     * @var array $isMsgwww stores string in array after expload by www
     * @var int $lengthMsghttp store length of $isMsghttp
     * @var int $lengthMsghttps store length of $isMsghttps
     * @var array $agentData stores data for logged in agent
     * @var int $lengthMsgwww store length of $isMsgwww
     * @var string $msg stores final message edited according to condition
     */

    public function fetchMsgForAdminFromUsersAction()
    {
        $paramData = $this->getrequest()->getParams();
        $agentId = $this->adminSession();
        $agentData = $this->getAgentData();
        $conversationCollection = Mage::getModel("chatsystem/conversation")->getCollection()
                                ->addFieldToFilter('forid', array('eq'=>$paramData["id"]))
                                ->addFieldToFilter('agent_id', array('eq'=>$agentId))
                                ->setOrder('id', 'DESC')
                                ->setPageSize($paramData["limit"]);
        $fullconversation = array();
        foreach ($conversationCollection as $value) {
            $isMsghttp = explode("http://", $value->getMessage());
            $isMsghttps = explode("https://", $value->getMessage());
            $isMsgwww = explode("www", $value->getMessage());
            $isMsgimageorfile = explode("#type#", $value->getMessage());
            // image
            if(isset($isMsgimageorfile[1]) && substr($isMsgimageorfile[1],0,5) == "image") {
                $lengthImage = strlen($isMsgimageorfile[1]);
                $msgImage = substr($isMsgimageorfile[1],5,$lengthImage);
            } else {
                $lengthImage = 0;
            }
            // file
            if(isset($isMsgimageorfile[1]) && substr($isMsgimageorfile[1],0,4) == "file") {
                $lengthFile = strlen($isMsgimageorfile[1]);
                $msgFile = substr($isMsgimageorfile[1],4,$lengthFile);
            } else {
                $lengthFile = 0;
            }

            if (isset($isMsghttp[1]) && $isMsghttp[1]!="") {
                $lengthMsghttp = strlen($isMsghttp[1]);
            } else {
                $lengthMsghttp = 0;
            }
            if (isset($isMsghttps[1]) && $isMsghttps[1]!="") {
                $lengthMsghttps = strlen($isMsghttps[1]);
            } else {
                $lengthMsghttps = 0;
            }
            if (isset($isMsgwww[1]) && $isMsgwww[1]!="") {
                $lengthMsgwww = strlen($isMsgwww[1]);
            } else {
                $lengthMsgwww = 0;
            }
            if (($isMsghttp[0] == "" && $lengthMsghttp > 0) || ($isMsghttps[0] == "" && $lengthMsghttps > 0) || ($isMsgwww[0] == "" && $lengthMsgwww > 0)) {
                if ($isMsgwww[0] == "" && strlen($isMsgwww[1]) > 0)
                    $msg = "<a href='http://".$value->getMessage()."' target='_blank'>".$value->getMessage()."</a>";
                else
                    $msg = "<a href='".$value->getMessage()."' target='_blank'>".$value->getMessage()."</a>";
            } elseif ($isMsgimageorfile[0] == "" && $lengthImage > 0) {
                $url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)."chatsystem/attachment/".$msgImage;
                $msg = "<a href='".$url."' target='_blank'><img src='".$url."' class='wk-img'></a>";
            } elseif ($isMsgimageorfile[0] == "" && $lengthFile > 0) {
                $url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)."chatsystem/attachment/".$msgFile;
                $msg = "<a href='".$url."' target='_blank'>".$msgFile."</a>";
            } else {
                $regex = "/^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/";
                if (preg_match($regex, $value->getMessage())) {
                    $msg = "<a href='mailto:".$value->getMessage()."' target='_blank'>".$value->getMessage()."</a>";
                }
                else
                $msg = $value->getMessage();
            }
            if ($value->getToType()=="Customer") {
                array_push(
                    $fullconversation,
                    array(
                        "from" => $value->getFromname(),
                        "message" =>$msg,
                        "created_at"=>date(
                            "M-d h:i A",
                            Mage::getModel('core/date')->timestamp($value->getCreatedAt())
                        ),
                        'image'=>$agentData['image'],
                        'isAdmin'=>'yes')
                );
            } else {
                array_push(
                    $fullconversation,
                    array(
                        "from" => $value->getFromname(),
                        "message" =>$msg,
                        "created_at"=>date(
                            "M-d h:i A",
                            Mage::getModel('core/date')->timestamp($value->getCreatedAt())
                        )
                    )
                );
            }
        }
        $this->getResponse()->clearHeaders()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(json_encode($fullconversation));
    }

     /**
     * @param void this action is to open grid of conversation at admin panel
     */


    public function individualchatAction()
    {
        $this->loadLayout()->_setActiveMenu("chatsystem");
        $this->getLayout()->getBlock("head")->setTitle($this->__("Individual Chat History"));
        $this->renderLayout();
    }

    /**
    * @param void export the conversation in CSV format
    * @var string $filename stores file name which is save when file is download
    * @var object $content stores object of CSV format with data
     */

    public function exportCsvAction()
    {
        $fileName = 'chat_history.csv';
        $content = $this->getLayout()->createBlock('chatsystem/adminhtml_chats_grid')
                        ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }


     /**
     * @param void export the conversation in xml format
    * @var string $filename stores file name which is save when file is download
    * @var object $content stores object of xml format with data
     */

    public function exportXmlAction()
    {
        $fileName = 'chat_history.xml';
        $content = $this->getLayout()->createBlock('chatsystem/adminhtml_chats_grid')
                        ->getExcelFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }


     /**
     * @param void export the individual customer's conversation in csv format
    * @var string $filename stores file name which is save when file is download
    * @var object $content stores object of CSV format with data
     */

    public function exportCsvindividualAction()
    {
        $fileName = 'chat_history.csv';
        $content = $this->getLayout()->createBlock('chatsystem/adminhtml_individualchat_grid')
                        ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

     /**
     * @param void export the individual customer's conversation in XML format
    * @var string $filename stores file name which is save when file is download
    * @var object $content stores object of xml format with data
     */


    public function exportXmlindividualAction()
    {
        $fileName = 'chat_history.xml';
        $content = $this->getLayout()->createBlock('chatsystem/adminhtml_individualchat_grid')
                        ->getExcelFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * To change status of admin
     */
    public function changeStatusAction() {
        $params = $this->getRequest()->getParams();
        $model = Mage::getModel('chatsystem/agent')
                ->getCollection()
                ->addFieldToFilter('agent_id',$params['agent_id'])
                ->getFirstItem();
        $model->setStatus($params['status']);
        $model->save();
        if($params['status'] == 0) {
            $assignCollection=Mage::getModel('chatsystem/totalassign')->getCollection()
                                ->addFieldToFilter('agent_id', array('eq'=>$params['agent_id']));
            foreach ($assignCollection as $agentassign) {
                $agentAssignId=$agentassign->getId();
            }
            $assignModel=Mage::getModel('chatsystem/totalassign')->load($agentAssignId);
            $assignModel->delete();
            $customerCollection=Mage::getModel("chatsystem/customerstatus")->getCollection()
                        ->addFieldToFilter("agent_id", array("eq"=>$params['agent_id']));
            if (count($customerCollection > 0)) {
                foreach ($customerCollection as $customer) {
                    $rowId=$customer->getId();
                    $model=Mage::getModel("chatsystem/customerstatus")->load($rowId)->delete();
                }
            }
        }
    }

    /**
     * update totalassign table
     */
    public function updateTotalassignAction() {
        $params = $this->getRequest()->getParams();
        $agentAssignCollection = Mage::getModel('chatsystem/totalassign')->getCollection()
                                ->addFieldToFilter('agent_id',array('eq'=>$params['agent_id']));
        if(count($agentAssignCollection) == 0) {
            $agentAssignModel = Mage::getModel('chatsystem/totalassign');
            $agentAssignModel->setAgentId($params['agent_id']);
            $agentAssignModel->setTotal(0);
            $agentAssignModel->save();
        }
    }

}

<?php
class Webkul_ChatSystem_IndexController extends Mage_Core_Controller_Front_Action
{

    /**
     * @param int $agentId Gives Collection of respective agent
     * @return object Return agent Collection
     * @var int $agentId sets agent id
     * @var object $agentCollection sets agent collection where agent id is equal to $agentId
     */

    public function agentCollection($agentId)
    {
        $agentCollection = Mage::getModel('chatsystem/agent')->getCollection()
                        ->addFieldToFilter('agent_id', array('eq'=>$agentId));
        return $agentCollection;
    }

    /**
     * @param void Gives customer session collection
     * @return object Return customer session Collection
     */

    public function customerSession()
    {
        return Mage::getSingleton('customer/session')->getCustomer()->getId();
    }

    /**
     * @param void check whether admin is logged in or not
     * @return int if any agent if login returns 1 if not returns 0
     * @var object $agentCollection gets agent collection where agent status is 1
     */

    public function isAnyAgentLogin()
    {
        $agentCollection = Mage::getModel('chatsystem/agent')->getCollection()
                            ->addFieldToFilter('status', array('eq'=>1));
        if (count($agentCollection)>0) {
            return 1;
        }
        else
            return 0;
    }

    /**
     * @param void Check for agent status to be online.
     * @return int Return 1 to be online and 0 to be offline.
     * @var int $customerId sets with logged in customer id
     * @var object $customerCollection sets customer status collection where
     * customer id is equal to $customerId and agentId not equal to 0
     * @var array $customerStatus save data array for particular customerCollection row
     * @var int $agentId save agentId form the $customerStatus
     * @var array $status save the status of agent, status save logged in or not and end saves to ebd chat or not
     */

    public function checkAdminStatusAction()
    {
        $status = array();
        $paramData = $this->getRequest()->getParams();
        $customerId = $this->customerSession();
        if ($customerId!="") {
            $customerCollection = Mage::getModel('chatsystem/customerstatus')->getCollection()
                ->addFieldToFilter('customer_id', array('eq'=>$customerId))
                ->addFieldToFilter('agent_id', array('neq'=>0));
            if (count($customerCollection)) {
                foreach ($customerCollection as $customerStatus) {
                    $agentId = $customerStatus->getAgentId();
                }
                $agentCollection = $this->agentCollection($agentId);
                foreach ($agentCollection as $agent) {
                    $status['status'] = $agent->getStatus();
                    $status['end'] = false;
                }
            } else {
                $status['status'] = $this->isAnyAgentLogin();
                $status['end'] = true;
            }
        } else {
            $status['status'] = $this->isAnyAgentLogin();
        }
        $this->getResponse()->clearHeaders()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(json_encode($status));
    }


    /**
     * @param void save customer status to start chat with agent
     * @return int success or not
     * @var int $customerId sets customerid from customer session
     * @var object $customerCollection it save the collection for customer
     * id equal to $customerId from customer statis
     * @var object $customerStatusModel it save the model for a customer if there is no customer row exist
     */

    public function saveCustomerStatusAction()
    {
        $status = array();
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $customerId = $this->customerSession();
            $customerCollection=Mage::getModel('chatsystem/customerstatus')->getCollection()
                                ->addFieldToFilter('customer_id', array('eq'=>$customerId));
            if (count($customerCollection) == 0) {
                $customerStatusModel = Mage::getModel('chatsystem/customerstatus');
                $customerStatusModel->setCustomerId($customerId);
                $customerStatusModel->setStatus(1);
                $customerStatusModel->save();
            }
            $status['status'] = 1;
        } else {
            $status['status'] = 0;
        }
        $this->getResponse()->clearHeaders()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(json_encode($status));
    }

    /**
     * @param void Check customer status for chat
     * @return int Return 1 for no and 0 for yes
     * @var int $customerId sets customer Id from customer session
     * @var object $customerCollection sets collection from customer status
     * model where custmer id equals to $customerId and agentId not equals to 0
     */

    public function checkCustomerStatusAction()
    {
        $status = array();
        $customerId = $this->customerSession();
        $customerCollection = Mage::getModel("chatsystem/customerstatus")->getCollection()
                            ->addFieldToFilter("customer_id", array("eq"=>$customerId))
                            ->addFieldToFilter("agent_id", array('neq'=>0));
        if (count($customerCollection) == 0) {
            $status['status'] = 1;
        } else {
            $status['status'] = 0;
        }
        $this->getResponse()->clearHeaders()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(json_encode($status));
    }


    /**
     * @param void assign agent to customer
     * @return object agent details which is assigned to customer
     * @var int $min it stores the total number of customer assigned to agent in total assign table
     * @var int $agentId stores the agent id
     * @var array $paramData stores the data which is send by the request i.e. by the form
     * @var int $customerId save the customer Id from customer session
     * @var object $agentCollection save the collection from agent table where status of agent is equals to 1
     * @var array $agent save the array of data from the each row of $agentCollection
     * @var array $agendId saves the agentId array get from $agentCollection
     * @var obejct countAssignCollection save collection of total assign table where agent id is in $agentId array
     * @var obejct $minAgentCollection save collection of row whose total is minimum in total assign table
     * @var object $maxAgentCollection save collection of row whose total is maximum in total assign table
     * @var int $minId stores minCollection row id
     * @var int $maxid stores maxCollection row id
     * @var int $minAgentId stores agentId from mincollection
     * @var int $maxAgentId stores agentId from MaxCollection
     * @var int $minCount store count field data from $minCollection
     * @var int $maxCount store Count field data from $maxCollection
     * @var int $finalAgentId store final agent id which id minimum
     * @var int $finalCount stores final count
     * @var  int $finalId stores the row of mincollection
     * @var array $sendData it stores data to return to phtml of further process
     * @var array $countData stores field to update row of total assign table
     * @var array $data stores field to update in customerStatus tabel
     * @var oject $countAssignModel model of total assign table
     */

    public function saveAgentAction()
    {
        $min = 0;
        $agentId = "";
        $sendData = array();
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $paramData = $this->getRequest()->getParams();
            $customerId = $this->customerSession();
            $agentCollection = Mage::getModel('chatsystem/agent')->getCollection()
                            ->addFieldToFilter('status', array('eq'=>1));
            foreach ($agentCollection as $agent) {
                $agentId[] = $agent->getAgentId();
            }
            if (count($agentId) > 0) {
                $countAssignCollection = Mage::getModel('chatsystem/totalassign')->getCollection()
                            ->addFieldToFilter('agent_id', array('in'=>array($agentId)));
                if (count($countAssignCollection)>0) {
                    $minAgentCollection = Mage::getModel('chatsystem/totalassign')
                                    ->getCollection()
                                    ->addFieldToFilter('agent_id', array('in'=>array($agentId)))
                                    ->setOrder('total', 'asc')
                                    ->setPageSize(1);
                    foreach ($minAgentCollection as $minAgent) {
                        $minId = $minAgent->getId();
                        $minAgentId = $minAgent->getAgentId();
                        $minCount = $minAgent->getTotal();
                    }
                    $maxAgentCollection = Mage::getModel('chatsystem/totalassign')
                                    ->getCollection()
                                    ->addFieldToFilter('agent_id', array('in'=>array($agentId)))
                                    ->setOrder('total', 'desc')
                                    ->setPageSize(1);
                    foreach ($maxAgentCollection as $maxAgent) {
                        $maxId = $maxAgent->getId();
                        $maxAgentId = $maxAgent->getAgentId();
                        $maxCount = $maxAgent->getTotal();
                    }
                    if ($maxCount != $minCount) {
                        $finalAgentId = $minAgentId;
                        $finalId = $minId;
                        $finalCount = $minCount;
                    } else {
                        $finalAgentId = $minAgentId;
                        $finalId = $minId;
                        $finalCount = $minCount;
                    }
                    $sendData['setAgent'] = 1;
                    $sendData['agent_id'] = $finalAgentId;
                    $customerCollection=Mage::getModel("chatsystem/customerstatus")->getCollection()
                                        ->addFieldToFilter("customer_id", array("eq"=>$customerId))
                                        ->addFieldToFilter("status", array('eq'=>1));
                    foreach ($customerCollection as $customer) {
                        $id = $customer->getId();
                    }
                    $data = array('agent_id'=>$finalAgentId);
                    $model = Mage::getModel('chatsystem/customerstatus')->load($id)->addData($data);
                    $model->setId($id)->save();
                    $finalCount++;
                    $countData = array('total'=>$finalCount);
                    $countAssignModel = Mage::getModel('chatsystem/totalassign')
                        ->load($finalId)->addData($countData);
                    $countAssignModel->setId($finalId)->save();
                    $agent = $this->getAgentProfile($finalAgentId);
                    $paramData['agent_id'] = $finalAgentId;
                    $paramData['agent_name'] = $agent['agent_name'];
                    $paramData['retrun'] = false;
                    $tempVariable = $this->saveConversation($paramData);
                    $sendData['about_agent'] = $agent['about_agent'];
                    $sendData['setAgent'] = 1;
                    $sendData['agent_id'] = $finalAgentId;
                    $sendData['agent_name'] = $agent['agent_name'];
                    $sendData['image'] = $agent['image'];
                    $sendData['conversation'] = $this->conversationCount($customerId, $finalAgentId);
                } else {
                    $this->sendEmail($paramData['message']);
                    $sendData['setAgent'] = 0;
                    $sendData['error'] = "No agent is available at the moment. Please start chat after some time.";
                }
            } else {
                $this->sendEmail($paramData['message']);
                $sendData['setAgent'] = 0;
                $sendData['error'] = "No agent is available at the moment. Please start chat after some time.";
            }
        } else {
            $sendData['setAgent'] = 0;
            $sendData['agent_id'] = "";
            $sendData['error'] = "There was an error to login";
        }
        $this->getResponse()->clearHeaders()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(json_encode($sendData));
    }

    /**
     * @param void It ends the chat when customer no mre want to chat
     * @return int success or not
     * @var obejct $countAssignModel model for total assign table
     * @var int $customerId store customer Id from session
     * @var object $customerCollection store collection of customerStatus where customer id is equals to $customerId
     * @var int $id store row id from $customerCollection
     * @var int $agentId stores agentId from $customerCollection
     * @var int $finalCount stores the count field data from $countAssignModel
     */

    public function endChatAction()
    {
        $status = array("status" => 0);
        $customerId = $this->customerSession();
        $customerCollection = Mage::getModel("chatsystem/customerstatus")->getCollection()
                            ->addFieldToFilter("customer_id", array("eq"=>$customerId));
        if (count($customerCollection)) {
            foreach ($customerCollection as $customer) {
                $id = $customer->getId();
                $agentId = $customer->getAgentId();
            }
            $customerModel = Mage::getModel("chatsystem/customerstatus")->load($id);
            $customerModel->delete();
            $countAssignCollection = Mage::getModel('chatsystem/totalassign')->getCollection()
                                ->addFieldToFilter('agent_id', array('eq'=>$agentId));
            if (count($countAssignCollection)) {
                foreach ($countAssignCollection as $assign) {
                    $id = $assign->getId();
                }
                $countAssignModel = Mage::getModel('chatsystem/totalassign')->load($id);
                $finalCount = $countAssignModel->getTotal();
                if ($finalCount == 0)
                    $finalCount = 0;
                else
                $finalCount--;
                $countData=array('total'=>$finalCount);
                $countAssignModel=Mage::getModel('chatsystem/totalassign')->load($id)->addData($countData);
                $countAssignModel->setId($id)->save();
                $status['status'] = 1;
            } else {
                $status['status'] = 1;
            }
        } else {
            $status['status'] = 1;
        }
        $this->getResponse()->clearHeaders()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(json_encode($status));
    }
    /**
     * @param void Save comment for agent given by customer
     * @return int Return success
     * @var array $paramData sets the data comming from request
     * @var int $customerId saves customer id from customer session
     * @var object $collection sets collection for reviews where customerId
     * equals to $customerID and agentId equals to $paramData['agentId']
     * @var object $model model for review table
     * @var array $review store data for particular row of $collection
     */

    public function saveCommentAction()
    {
        $starts = array("status" => 0);
        $paramData = $this->getRequest()->getParams();
        if ($paramData['comment']!="" || $paramData['rate']!=0) {
            $customerId = $this->customerSession();
            $collection = Mage::getModel("chatsystem/review")->getCollection()
                        ->addFieldToFilter("customer_id", array('eq'=>$customerId))
                        ->addFieldToFilter("agent_id", array("eq"=>$paramData['agent_id']));
            foreach ($collection as $review) {
                $id = $review->getId();
            }
            if (count($collection)==0) {
                $model = Mage::getModel("chatsystem/review");
                $model->setAgentId($paramData['agent_id']);
                $model->setCustomerId($customerId);
                $model->setRating($paramData['rate']);
                $model->setComment($paramData['comment']);
                $model->setStatus("pending");
                $model->save();
                $status['status'] = 1;
            } else {
                $model = Mage::getModel("chatsystem/review")->load($id);
                $model->setComment($paramData['comment']);
                $model->setRating($paramData['rate']);
                $model->save();
                $status['status'] = 1;
            }
        } else {
            $status['status'] = 1;
        }
        $this->getResponse()->clearHeaders()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(json_encode($status));
    }

    /**
     * @param void calculate total rate for agent given by different customers
     * @return object total rates in % and total rate in number
     * @var int $customerId saves customer id from customer session
     * @var array $paramData sets the data comming from request
     * @var array $review store data for particular row of $collection
     * @var object $collection sets collection for reviews where customerId
     * equals to $customerID and agentId equals to $paramData['agentId']
     * @var int $finalrate store the average of rate
     * @var array $send_data store data to return to phtml for futher processing
     * @var int @rate store total rate from collection
     */

    public function calculateRatesAction()
    {
        $paramData = $this->getRequest()->getParams();
        $sendData = array();
        $rate = "";
        $customerId = $this->customerSession();
        $collection = Mage::getModel("chatsystem/review")->getCollection()
                        ->addFieldToFilter("agent_id", array("eq"=>$paramData['agent_id']));
        foreach ($collection as $review) {
            $id = $review->getId();
            $rate = $rate + $review->getRating();
        }
        if (count($collection) == 0) {
            $finalRate = ($rate)*20;
        } else {
            $finalRate = ($rate/count($collection))*20;
        }
        $sendData['rate'] = $finalRate;
        $sendData['total_rate'] = count($collection);
        $this->getResponse()->clearHeaders()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(json_encode($sendData));
    }

    /**
     * @param int $agentId Agent profile data of respective agent id
     * @return array Return agent name and agent image
     * @var array $agentData stores data from agent collection
     * @var object $agentCollection stores collection of agent where agentId equals to $agentId
     * @var array $agent store data in array for each particular row in $agentCollection
     */

    public function getAgentProfile($agentId)
    {
        $agentData = array();
        $agentCollection = $this->agentCollection($agentId);
        foreach ($agentCollection as $agent) {
            $agentData['agent_name'] = $agent->getFirstName()." ".$agent->getLastName();
            $agentData['about_agent'] = $agent->getAboutAgent();
            if ($agent->getImage() != "") {
                $this->imageResize($agent->getImage(), 'media', 'resize', 100, 100);
                $agentData['image']=Mage::getBaseurl('media').DS.('resize/').$agent->getImage();
            }
            else
                $agentData['image']=Mage::getBaseUrl('js').'chatsystem/images/pic-client.png';
        }
        return $agentData;
    }

    /**
     * @var string $imgName contain image name
     * @var string $path contain source path
     * @var string $newPath contain new path for resized image
     * @var int $width width for resizing image
     * @var int $height height for resizing image
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
     * @param array $data save message send by customer to agent
     * @return int Return success or not
     * @var array $paramData argument variable stores data to save
     * @var object $helper stores obkect for helper
     * @var int $customerId store customer Id for customer session
     * @var object $model create a model of conversation table
     * @var object $adminNotificationCollection store collection of admin notify
     * table where user_id equals to for id and agentId equals to agent_id
     * @var array $notification store array for particular row
     * @var int $id stores row id of $notification
     */
    public function saveConversation($paramData)
    {
        $id = "";
        $helper = Mage::helper('chatsystem');
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {

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

            $customerId = $this->customerSession();
            if ($customerId == $paramData["forid"]) {
                $model = Mage::getModel("chatsystem/conversation");
                $model->setFromname($paramData["fromname"]);
                $model->setForid($paramData["forid"]);
                $model->setToname($paramData['agent_name']);
                if ($paramData['file_name']) {
                    $model->setMessage("#type#".$paramData["file_type"].$file_name);
                } else {
                    $model->setMessage($paramData["message"]);
                }
                $model->setAgentId($paramData['agent_id']);
                $model->setAgentName($paramData['agent_name']);
                $model->settoType('Admin');
                $model->setCreatedAt(time());
                $model->save();
                $adminNotificationCollection = Mage::getModel("chatsystem/adminnotify")->getCollection()
                                            ->addFieldToFilter("userid", $paramData["forid"])
                                            ->addFieldToFilter("agent_id", $paramData['agent_id']);
                foreach ($adminNotificationCollection as $notification) {
                    $id=$notification->getId();
                }
                if ($id > 0) {
                    $model = Mage::getModel("chatsystem/adminnotify")->load($id);
                    $model->setUserid($paramData["forid"]);
                    $model->setAgentId($paramData['agent_id']);
                    $model->setStatus(1);
                    $model->save();
                } else {
                    $model = Mage::getModel("chatsystem/adminnotify");
                    $model->setUserid($paramData["forid"]);
                    $model->setAgentId($paramData['agent_id']);
                    $model->setStatus(1);
                    $model->save();
                }
                if (isset($paramData['return'])) {
                    return;
                } else {
                    return 1;
                }
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    /**
     * @param void this action hits when customer want to save conversation
     * @var array $paramData store data of request
     */

    public function saveConversationAction()
    {
        $paramData = $this->getRequest()->getParams();
        $status = array();
        $status['status'] = $this->saveConversation($paramData);
        $this->getResponse()->clearHeaders()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(json_encode($status));
    }

    /**
     * @param string $message send mail to admin when to agent is logged in
     * @var string $message store message which is send by the customer
     * @var string $email store email id of admin
     * @var string $name store admin name
     * @var object $emailTemplate stores block layout object
     * @var array $templateVariable stores variables for sending template
     */

    public function sendEmail($message)
    {
        $helper = Mage::helper('chatsystem');
        $email = $helper->getAdminEmail();
        $name = $helper->getAdminName();
        $emailTemplate = Mage::getModel("core/email_template")->loadDefault("notification_mailq");
        $templateVariable = array();
        $customer = Mage::getModel('customer/session')->getCustomer();
        $emailTemplate->setSenderName($customer->getFirstname());
        $emailTemplate->setSenderEmail($customer->getEmail());
        $templateVariable["admin_name"] = "admin";
        $templateVariable["msg"] = "You have a new message from customer ".
            $customer->getFirstname()." ".$customer->getLastname();
        $templateVariable["message"] = "Message is \"".$message."\"";
        $templateVariable['link'] = "<a href=".Mage::getBaseurl().'admin'.">Click Here to login!</a>";
        $emailTemplate->getProcessedTemplate($templateVariable);
        $emailTemplate->send($email, $name, $templateVariable);
    }

    /**
     * @param void send mail to admin when customer report for an agent
     * @var string $email store email id of admin
     * @var string $name store admin name
     * @var object $emailTemplate stores block layout object
     * @var array $templateVariable stores variables for sending template
     * @var string $agentName stores agentname comes from $agent
     * @var array $agent store data for partoicular row from $agentCollection
     * @var object $agentCollection stores collection of agent table where agentId equals to $paramData['agent_id']
     */

    public function reportToManagerAction()
    {
        $paramData = $this->getRequest()->getParams();
        $helper = Mage::helper('chatsystem');
        $email = $helper->getAdminEmail();
        $name = $helper->getAdminName();
        $emailTemplate = Mage::getModel("core/email_template")->loadDefault("report_mail");
        $templateVariable = array();
        $agentCollection = $this->agentCollection($paramData['agent_id']);
        foreach ($agentCollection as $agent) {
            $agentName = $agent->getFirstName()." ".$agent->getLastName();
        }
        $customer = Mage::getModel('customer/session')->getCustomer();
        $emailTemplate->setSenderName($customer->getFirstname());
        $emailTemplate->setSenderEmail($customer->getEmail());
        $templateVariable["admin_name"] = "admin";
        $templateVariable["msg"] = "You have a new message from customer ".
        $customer->getFirstname()." ".$customer->getLastname();
        $templateVariable["msg"].= ".</br>He sent a report about an agent.</br> Agent Name: ".$agentName;
        $templateVariable['link'] = "<a href=".Mage::getBaseurl().'admin'.">Click Here to login!</a>";
        $templateVariable["message"] = "Message is \"".$paramData['message']."\"";
        $emailTemplate->getProcessedTemplate($templateVariable);
        $emailTemplate->send($email, $name, $templateVariable);
        $status = array('status' => 1);
        $this->getResponse()->clearHeaders()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(json_encode($status));
    }

    /**
     * @param void check if there is new message for customer or not
     * @return string yes or not
     * @var array $paramData store data send by the request
     * @var int $status stores status of $notification
     * @var array $notification store data for particular row of $adminNotificationCollection
     * @var object $adminNotificationCollection stores collection of admin
     * notifications where userid equals to $paramData["userid"] and agent id
     * equals to $paramData['agent_id']
     */

    public function checkIfNewMessageThereAction()
    {
        $returnStatus = '';
        $status = "";
        $paramData = $this->getRequest()->getParams();
        $adminNotificationCollection = Mage::getModel("chatsystem/usernotify")->getCollection()
            ->addFieldToFilter("userid", $paramData["userid"])
            ->addFieldToFilter('agent_id', array('eq'=>$paramData['agent_id']));
        foreach ($adminNotificationCollection as $notification) {
            $status=$notification->getStatus();
        }
        if ($status == 1) {
            $returnStatus = "yes";
        } else {
            $returnStatus = "no";
        }
        $this->getResponse()->clearHeaders()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody($returnStatus);
    }

    /**
     * @param void Check is there is new message for customer
     * @return int Return no of notifications for customer
     * @var int $customerId stores customer Id for which conversation count
     * @var int $agentId stores agentId for which get conversation
     * @var object $conversation store collection of conversation where
     * forid equals to $customerId and agent_id equals to $agentId
     * @var int $noofConversation store total row fetched
     */

    public function conversationCount($customerId, $agentId)
    {
        $conversation = Mage::getModel("chatsystem/conversation")->getCollection()
                        ->addFieldToFilter("forid", array('eq'=>$customerId))
                        ->addFieldToFilter("agent_id", array('eq'=>$agentId));
        $noOfConversation=count($conversation);
        return $noOfConversation;
    }
    /**
     * @param void to check the number of conversation and no of notifications
     * @var array $paramData store data send by the request
     * @var int $customerId stores customer Id from customer session
     * @var object $usernotify stores collection of user notification
     * table where userid equals to $customerId , agent_id equal to $paramData['agent_id'] and status equals to 1
     * @var int $noOfNotification count total rows in $usernotify
     * @var array $sendData stores the field to send data it back to phtml for further process
     */

    public function newMessageAction()
    {
        $paramData = $this->getRequest()->getParams();
        $sendData = array();
        $customerId = $this->customerSession();
        $usernotify = Mage::getModel("chatsystem/usernotify")->getCollection()
                    ->addFieldToFilter("userid", $customerId)
                    ->addFieldToFilter('agent_id', array('eq'=>$paramData['agent_id']))
                    ->addFieldToFilter("status", 1);
        $noOfNotification = count($usernotify);
        $sendData['noOfConversation']=$this->conversationCount($customerId, $paramData['agent_id']);
        $sendData['noOfNotification']=$noOfNotification;
        $this->getResponse()->clearHeaders()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(json_encode($sendData));
    }

    /**
     * @param void change the status of message when it is read by customer
     * @var array $paramData store data send by the request
     * @var int $id stores row id of $notification
     * @var array $notification stores data of particular row of $adminNotificationCollection
     * @var object $adminNotificationCollection stores collection of user
     * notification table where userid equals to $paramData["userid"] and agent_id $paramData['agentId']
     * @var object $model set a model of user notification table
     */

    public function notifyMsgIsReadnowAction()
    {
        $id = "";
        $paramData = $this->getRequest()->getParams();
        $adminNotificationCollection = Mage::getModel("chatsystem/usernotify")->getCollection()
                                ->addFieldToFilter("userid", $paramData["userid"])
                                ->addFieldToFilter('agent_id', $paramData['agentId']);
        foreach ($adminNotificationCollection as $notification) {
            $id = $notification->getId();
        }
        $model = Mage::getModel("chatsystem/usernotify")->load($id);
        $model->setStatus(0);
        $model->save();
    }

    /**
    * @param void print conversation in a PDF format
    * @var string $filename stores file name which is save when file is download
    * @var object $content stores object of pdf format with data
    */

    public function printConversationAction()
    {
        $fileName = 'conversation.pdf';
        $content = $this->getLayout()->createBlock('chatsystem/grid')->getPdfFile();
        $this->_sendUploadResponse($fileName, $content);
    }

    /**
    * @param void Use to download the files, in respective format
    */

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK', '');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename=' . $fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
    }


    /**
     * @var array $paramData store data send by the request
     * @param void fetch messages for user send by agent which is assigned to user
     * @var array $agent save data of an agent with agentId $paramData['agent_id']
     * @var object $conversationCollection stores collection of conversation
     * table where forid equals to $paramData['id'], agent_id equals to $paramData['agent_id']
     * @var array $fullconversation stores the full conversation data in json format
     * @var array $isMsghttp stores string in array after expload by http://
     * @var array $isMsghttp stores string in array after expload by http://
     * @var array $isMsgwww stores string in array after expload by www
     * @var int $lengthMsghttp store length of $isMsghttp
     * @var int $lengthMsghttps store length of $isMsghttps
     * @var int $lengthMsgwww store length of $isMsgwww
     * @var string $msg stores final message edited according to condition
     */

    public function fetchMsgForAdminFromUsersAction()
    {
        $paramData = $this->getrequest()->getParams();
        $agent = $this->getAgentProfile($paramData['agent_id']);
        $conversationCollection = Mage::getModel("chatsystem/conversation")->getCollection()
                                ->addFieldToFilter('forid', $paramData['id'])
                                ->addFieldToFilter('agent_id', $paramData['agent_id'])
                                ->setOrder('id', 'DESC')
                                ->setPageSize($paramData['limit']);
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
                        "image"=>$agent['image'],
                        'isAdmin'=>'yes')
                );
            } else {
                array_push(
                    $fullconversation,
                    array(
                        "from" => $value->getFromname(),
                        "message" =>$msg,
                        "created_at"=> date(
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
     * [updateStatusAction to update status of customer]
     */
    public function updateStatusAction() {
        $params = $this->getRequest()->getParams();
        if(Mage::getSingleton('customer/session')->isLoggedIn()) {
            $customerId = Mage::getSingleton('customer/session')->getId();
            $collection = Mage::getModel('chatsystem/customerstatus')
                    ->getCollection()
                    ->addFieldToFilter('customer_id',$customerId);
            foreach ($collection as $customer_data) {
                $customer_data->setStatus($params['status']);
                $customer_data->save();
            }
        }

    }
}

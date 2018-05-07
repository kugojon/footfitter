<?php
    class Webkul_ChatSystem_Block_Adminhtml_Individualchat_Grid extends Mage_Adminhtml_Block_Widget_Grid {

       /**
      * @param void Construct block header for individual Chats grid
      */


      public function __construct() {
          parent::__construct();
          $this->setDefaultSort("id");
          $this->setDefaultDir("ASC");
          $this->setSaveParametersInSession(true);
          $this->setFilterVisibility(true);
      }

      /**
      * @param void Prepare collection for individual Chats grid
      */

      protected function _prepareCollection() {
        $id = $this->getRequest()->getParam('id');
        $user = Mage::getSingleton("admin/session");
        $agentId = $user->getUser()->getUserId();
        $chatmodel = Mage::getModel("chatsystem/agent")->getCollection();
        $temp=0;
        foreach ($chatmodel as $model) {
          if($agentId==$model->getAgentId()){
            $temp=1;
            break;
          }
        }
        if($temp==1){
          $collection = Mage::getModel("chatsystem/conversation")->getCollection()
            ->addFieldToFilter("agent_id",$agentId)
            ->addFieldToFilter("forid",$id);
        }else{
            $collection = Mage::getModel("chatsystem/conversation")->getCollection()
              ->addFieldToFilter("forid",$id);
        }
        $this->setCollection($collection);
        parent::_prepareCollection();

        foreach ($this->getCollection() as $item) {
            $isMsgimageorfile = explode("#type#", $item->message);

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

            if ($isMsgimageorfile[0] == "" && $lengthImage > 0) {
                $url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)."chatsystem/attachment/".$msgImage;
                $item->message = "<a href='".$url."' target='_blank'><img src='".$url."' width='100px'></a>";
            } elseif ($isMsgimageorfile[0] == "" && $lengthFile > 0) {
                $url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)."chatsystem/attachment/".$msgFile;
                $item->message = "<a href='".$url."' target='_blank'>".$msgFile."</a>";
            }
        }
      }

       /**
      * @param void prepares column for individual Chats grid
      */

      protected function _prepareColumns() {

        $this->addColumn("fromname", array(
          "header"    => Mage::helper("chatsystem")->__("From"),
          "index"     => "fromname",
          'sortable'  => true,
          "width"     => "300px"
        ));

        $this->addColumn("toname", array(
          "header"    => Mage::helper("chatsystem")->__("To"),
          "align"     => "left",
          "index"     => "toname",
          "width"     => "300px"
        ));

        $this->addColumn("message", array(
          "header"    => Mage::helper("chatsystem")->__("Message"),
          "index"     => "message",
          'type'      =>'text'
        ));

        $this->addColumn("created_at", array(
          "header"    => Mage::helper("chatsystem")->__("Dated On"),
          "index"     => "created_at",
          "type"      => "datetime",
          "align"     => "left",
          "width"     => "200px",
        ));


        $this->addExportType('*/*/exportCsvindividual', Mage::helper('chatsystem')->__('CSV'));
        $this->addExportType('*/*/exportXmlindividual', Mage::helper('chatsystem')->__('XML'));
        return parent::_prepareColumns();
      }

      protected function _prepareMassaction() {
          return $this;
      }

  }

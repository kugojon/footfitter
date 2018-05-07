<?php
    class Webkul_ChatSystem_Block_Adminhtml_Chats_Grid extends Mage_Adminhtml_Block_Widget_Grid {


      /**
      * @param void Construct block header for Chats grid
      */

      public function __construct() {
          parent::__construct();
          $this->setId("chats_grid");
          $this->setDefaultSort("created_at");
          $this->setDefaultDir("DESC");
          $this->setUseAjax(true);
          $this->setSaveParametersInSession(true);
      }

      /**
      * @param void Prepare collection for conversation grid
      */

      protected function _prepareCollection() {
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
            ->addFieldToFilter("agent_id",$agentId);
        }else{
            $collection = Mage::getModel("chatsystem/conversation")->getCollection();
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
                  $item->message = "<img src='".$url."' width='100px'>";
              } elseif ($isMsgimageorfile[0] == "" && $lengthFile > 0) {
                  $url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)."chatsystem/attachment/".$msgFile;
                  $item->message = $msgFile;
              }
          }

      }


      /**
      * @param void prepares column for chats grid
      */

      protected function _prepareColumns() {

        $this->addColumn("fromname", array(
          "header"    => Mage::helper("chatsystem")->__("From"),
          "index"     => "fromname",
          'sortable'  =>  true,
          "width"     => "300px",
        ));
        $this->addColumn("toname", array(
          "header"    => Mage::helper("chatsystem")->__("To"),
          "align"     => "left",
          "index"     => "toname",
          'sortable'  =>  true,
          "width"     => "300px"
        ));

        $this->addColumn("message", array(
          "header"    => Mage::helper("chatsystem")->__("Message"),
          "index"     => "message",
          'type'  =>'text',
          'sortable'  =>  true,

        ));

        $this->addColumn("created_at", array(
          "header"    => Mage::helper("chatsystem")->__("Dated On"),
          "index"     => "created_at",
          "type"      => "datetime",
          "align"     => "left",
          'sortable'  =>  true,
          "width"     => "200px",
        ));


        $this->addExportType('*/*/exportCsv', Mage::helper('chatsystem')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('chatsystem')->__('XML'));
        return parent::_prepareColumns();
      }

      protected function _prepareMassaction() {
          return $this;
      }

      /**
      * @param void calls when click on particular row to get individual customer chat
      */

      public function getRowUrl($row) {
        return $this->getUrl('*/*/individualchat', array('id' => $row->getForid()));
      }

      /**
      * @param void calls when apply filter by ajax
      */

      public function getGridUrl() {
        return $this->getUrl("*/*/grid", array("_current" => true));
      }

  }

<?php
    class Webkul_ChatSystem_Block_Adminhtml_Review_Grid extends Mage_Adminhtml_Block_Widget_Grid {


      /**
      * @param void Construct block header for Review and comments grid
      */

      public function __construct() {
          parent::__construct();
          $this->setId("review_grid");
          $this->setUseAjax(true);
          $this->setDefaultSort("id");
          $this->setDefaultDir("ASC");
          $this->setSaveParametersInSession(true);
      }

      /**
      * @param void Prepare collection for reviews and comment's grid
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
          $collection = Mage::getModel("chatsystem/review")->getCollection()
            ->addFieldToFilter("agent_id",$agentId);
        }else{
            $collection = Mage::getModel("chatsystem/review")->getCollection();
        }
          $prefix = Mage::getConfig()->getTablePrefix();
          $collection->getSelect()
            ->join(array("c1" => $prefix."wk_cs_agent"),"c1.agent_id = main_table.agent_id","c1.first_name")
            ->join(array("c2" => $prefix."wk_cs_agent"),"c2.agent_id = main_table.agent_id","c2.last_name")
            ->columns(new Zend_Db_Expr("CONCAT(`c1`.`first_name`, ' ',`c2`.`last_name`) AS agentname"));

        $collection->addFilterToMap("agentname","CONCAT(c1.first_name,' ',c2.last_name)");

        $fnameid = Mage::getModel("eav/entity_attribute")->loadByCode("1", "firstname")->getAttributeId();
        $lnameid = Mage::getModel("eav/entity_attribute")->loadByCode("1", "lastname")->getAttributeId();
        $collection->getSelect()
          ->join(
            array(
              "ce1" => $prefix."customer_entity_varchar"
            ),
            "ce1.entity_id = main_table.customer_id",
            array("fname" => "value")
          )->where("ce1.attribute_id = ".$fnameid)
          ->join(
            array(
              "ce2" => $prefix."customer_entity_varchar"
            ),
            "ce2.entity_id = main_table.customer_id",
            array("lname" => "value")
          )->where("ce2.attribute_id = ".$lnameid)
          ->columns(new Zend_Db_Expr("CONCAT(`ce1`.`value`, ' ',`ce2`.`value`) AS customername"));
        $collection->addFilterToMap("customername","CONCAT(`ce1`.`value`, ' ',`ce2`.`value`)");
        $this->setCollection($collection);
        parent::_prepareCollection();
      }

       /**
      * @param void prepares column for reviews and comment's  grid
      */

      protected function _prepareColumns() {

        $this->addColumn("agentname", array(
          "header"    => Mage::helper("chatsystem")->__("Agent Name"),
          "index"     => "agentname",
          "sortable"  => true,
          "width"     => "300px",
        ));
        $this->addColumn("customername", array(
          "header"    => Mage::helper("chatsystem")->__("Customer Name"),
          "align"     => "left",
          "sortable"  => true,
          "index"     => "customername",
          "width"     => "300px"
        ));
        $this->addColumn("rating", array(
          "sortable"  => true,
          "header"    => Mage::helper("chatsystem")->__("Rates out of 5"),
          "index"     => "rating"
        ));
        $this->addColumn("comment", array(
          "sortable"  => true,
          "header"    => Mage::helper("chatsystem")->__("Comment"),
          "index"     => "comment"
        ));
      }
      /**
      * @param void calls when apply filter by ajax
      */

       public function getGridUrl() {
        return $this->getUrl("*/*/grid", array("_current" => true));
      }
  }

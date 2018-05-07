<?php
    class Webkul_ChatSystem_Block_Grid extends Mage_Adminhtml_Block_Widget_Grid {

      public function __construct() {
          parent::__construct();
          $this->setDefaultSort("id");
          $this->setDefaultDir("ASC");
          $this->setSaveParametersInSession(true);
      }

      /**
     * @param void it prepares collection for grid
     * @return to parent collection
     * @var object $customer saves customer session
     * @var int $id sets customer id
     * @var object $customerCollection gets customer status collection to get agent id
     * @var agent_id saves agent id sets for particular customer
     * @var object $conversation sets conversation collection for the particular customer id and agent id
     * @var array customerStatus saves array of wach customerstatus array
     */


      protected function _prepareCollection() {
          $customer = Mage::getModel('customer/session')->getCustomer(); 
          $id=$customer->getEntityId();
          $customerName=$customer->getFirstname();
          $customerCollection=Mage::getModel('chatsystem/customerstatus')->getCollection()
                      ->addFieldToFilter('customer_id',array('eq'=>$id));
          foreach ($customerCollection as $customerStatus) {
            $agent_id=$customerStatus->getAgentId();
          }
          $conversation=Mage::getModel("chatsystem/conversation")->getCollection()
                ->addFieldToFilter("forid",array('eq'=>$id))
                ->addFieldToFilter("agent_id",array('eq'=>$agent_id))
                ->setOrder('id','ASC');
          $this->setCollection($conversation);
          return parent::_prepareCollection();
      }

      protected function _prepareColumns() {

        $this->addColumn("fromname", array(
          "header"    => Mage::helper("chatsystem")->__("From"),
          "index"     => "fromname",
          "width"     => "300px",
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
        return parent::_prepareColumns();
      }

      /**
     * @param void use to create PDF file
     * @return object returnn pdf format object
     * @var object $customer sets customer session
     * @var int $id sets customer id
     * @var object $customerCollection gets customer status collection to get agent id
     * @var agent_id saves agent id sets for particular customer
     * @var array customerStatus saves array of wach customerstatus array
     * @var object $agentCollection sets agent collection according to agentid
     * @var array $agent sets array of each agent from agent collection object
     * @var object $pdf is sets an pdf object
     * @var string $agentname sets agent name
     * @var int $i use for pdf row
     * @var int $j use for pdf column
     * @var string $header saves data for header of pdf
     * @var $font sets font for PDF
     * @var $page sets page size 
     * @var int $width sets width of page
     * @var int $maxlength sets length of maximum characters from agent name 
     */

        public function getPdfFile(){
          $customer = Mage::getModel('customer/session')->getCustomer();
          $id=$customer->getEntityId();
          $customerName=$customer->getFirstname()." ".$customer->getLastname();
          $customerCollection=Mage::getModel('chatsystem/customerstatus')->getCollection()
                      ->addFieldToFilter('customer_id',array('eq'=>$id));
          foreach ($customerCollection as $customerStatus) {
            $agent_id=$customerStatus->getAgentId();
          }
          $agentCollection=Mage::getModel('chatsystem/agent')->getCollection()
                           ->addFieldToFilter('agent_id',array('eq'=>$agent_id));
          foreach ($agentCollection as $agent) {
                $agentName=$agent->getFirstName()." ".$agent->getLastName();
          }
          $this->_isExport = true;
          $this->_prepareGrid();
          $this->getCollection()->getSelect()->limit();
          $this->getCollection()->setPageSize(0);
          $this->getCollection()->load();
          $this->_afterLoadCollection();

          $pdf = new Zend_Pdf();
          $page = new Zend_Pdf_Page(Zend_Pdf_Page::SIZE_A4);
          $font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES);
          $page->setFont($font, 18);
          $width = $page->getWidth();
        
          $i=20;
          $j=20;
          $header = "Conversation between ".$agentName."(agent) and ".$customerName; 
          if(strlen($agentName)>strlen($customerName)){
            $maxLength=strlen($agentName);
            $totype="Customer";
          }
          else{
            $maxLength=strlen($customerName);
            $totype="Admin";
          }
          $widthabc = $font->widthForGlyph($font->glyphNumberForCharacter($header));
            $restWidth= intval(($width-10)/($widthabc/$font->getUnitsPerEm()*18));
          $text=array();
          foreach (Mage::helper('core/string')->str_split($header,$restWidth) as $_value) {
            $text[] = $_value;
          }
            foreach ($text as $part) {
              $page->drawText($part, $i, $page->getHeight()-$j);
              $j+= 25;
            } 
          $j+=10;
          foreach ($this->getCollection() as $value) {
            if($maxLength>20){ 
                  $maxLength=20;
              }
              if(strlen($value->getFromname())>20){
                  foreach (Mage::helper('core/string')->str_split($value->getFromname(),$maxLength, true, true) as $_name) {
                    $name[] = $_name;
                }
                $value->setFromname($name[0]);
              }
            $page->setFont($font, 12);
            $j+=20;
            $i=10;
            if($j+20>=$page->getHeight()){
              $pdf->pages[] = $page;
              $page = new Zend_Pdf_Page(Zend_Pdf_Page::SIZE_A4);
              $font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES);
              $page->setFont($font, 12);
              $width = $page->getWidth();
              $j=20;
            }
            $page->drawText($value->getFromname()." : ", $i, $page->getHeight()-$j);
            $widthabc = $font->widthForGlyph($font->glyphNumberForCharacter($value->getFromname()));
            $i+=($widthabc/$font->getUnitsPerEm()*12)*$maxLength+20;
            $restWidth= intval(($width-$i)/($widthabc/$font->getUnitsPerEm()*12))+5;
            $text=array();
            $msg = $value->getMessage();
            foreach (Mage::helper('core/string')->str_split($msg,$restWidth, true, true) as $_value) {
              foreach (explode("</br>",$_value) as $_text) {
                  $text[] = $_text;
                }
              }
            foreach ($text as $part) {
              $page->drawText($part, $i, $page->getHeight()-$j);
              $j+= 15;
            }
            $page->setFont($font, 8);
            $page->drawText($this->__("Sent at : ").date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp($value->getCreatedAt())),$i, $page->getHeight()-$j);
          }
          // die();
          $pdf->pages[] = $page;
          return $pdf->render();
        }
  }



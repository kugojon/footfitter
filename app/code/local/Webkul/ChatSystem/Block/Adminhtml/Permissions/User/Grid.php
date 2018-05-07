<?php
	class Webkul_ChatSystem_Block_Adminhtml_Permissions_User_Grid extends Mage_Adminhtml_Block_Widget_Grid 
	{

		 /**
      * @param void Construct block header for agent grid
      */

		public function __construct()	{
	      parent::__construct();
          $this->setId("agent_grid");
          $this->setUseAjax(true);
          $this->setDefaultSort("id");
          $this->setDefaultDir("ASC");
          $this->setSaveParametersInSession(true);
	    }

	    /**
		 * @param void prepare collection for grid
		 * @var string $baseUrl sets baseurl of media folder
		 * @var object $collection sets collection for all agent
		 * @var string $img sets image url for particular agent
		 * 
		 */

	    protected function _prepareCollection() {
	        $baseUrl = Mage::getBaseUrl("media");
	        $collection = Mage::getModel("chatsystem/agent")->getCollection();
	        $this->setCollection($collection);
	        parent::_prepareCollection();
	        foreach ($collection as $key => $value) {
	           $img = $value->getImage();
			   if ($img=='') {
				   	$jsUrl = Mage::getBaseUrl('js');
	        		$value->x = "<img src='".$jsUrl.'/chatsystem/images/pic-client.png'."', height=100px, width=200px>";
			   } else {
		           $value->x = "<img src='".$baseUrl.$img."', height=100px, width=200px>";
			   }
	        }
      	}
	    protected function _prepareColumns()
	    {
	        $this->addColumn('agent_id', array(
	            'header'    => Mage::helper('adminhtml')->__('Agent ID'),
	            'width'     => 5,
	            'align'     => 'right',
                "sortable"  => true,
				'index'     => 'agent_id'
	        ));

	        $this->addColumn('username', array(
	            'header'    => Mage::helper('adminhtml')->__('User Name'),
	            'width'     => 200,
	            'index'     => 'username'

	        ));

	        $this->addColumn('first_name', array(
	            'header'    => Mage::helper('adminhtml')->__('First Name'),
	            'width'     => 200,
	            'index'     => 'first_name'
	        ));

	        $this->addColumn('last_name', array(
	            'header'    => Mage::helper('adminhtml')->__('Last Name'),
	            'width'     => 200,
                "sortable"  => true,
                'index'     => 'last_name'
	        ));

	        $this->addColumn('email', array(
	            'header'    => Mage::helper('adminhtml')->__('Email'),
	            'align'     => 'left',
                "sortable"  => true,
                'index'     => 'email'
	        ));
	         $this->addColumn("x", array(
                "header"    => Mage::helper("chatsystem")->__("Image"),
                "align"     => "center",
                "index"     => "x",
                "type"      => "text",
                "sortable"  => false,
                "filter"	=>false,
                "width"     => "200px"
            ));

	        return parent::_prepareColumns();
	    }

	    /**
		 * @param int $row returns rowurl when click on a row in grid to edit agent
		 * @return string return url for particular agent profile edit page
		 */

	    public function getRowUrl($row){
	        return $this->getUrl('adminhtml/permissions_user/edit', array('user_id' => $row->getAgentId()));;
	    }

	    /**
		 * @param int $row to get grid url to apply filter by ajax
		 * @return url for grid
		 */

	    public function getGridUrl() {
	    	return $this->getUrl("*/*/grid", array("_current" => true));
	    }

	}
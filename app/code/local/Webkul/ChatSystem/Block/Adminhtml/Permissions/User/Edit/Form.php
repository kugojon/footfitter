<?php
	class Webkul_ChatSystem_Block_Adminhtml_Permissions_User_Edit_Form extends Mage_Adminhtml_Block_Permissions_User_Edit_Form
	{

	    protected function _prepareForm()
	    {
		    parent::_prepareForm();
		    $form = new Varien_Data_Form(array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post',"enctype" => "multipart/form-data"));
	        $form->setUseContainer(true);
	        $this->setForm($form);
	        return $this;
	    }
	}

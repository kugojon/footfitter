<?php
	class Webkul_ChatSystem_Block_Adminhtml_Permissions_User_Edit_Tab_Main extends Mage_Adminhtml_Block_Permissions_User_Edit_Tab_Main {

        protected function _prepareForm() {
        $model = Mage::registry('permissions_user');
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('user_');
        $userId=$model->getUserId();
            $data=Mage::getModel("chatsystem/agent")->getCollection()
            ->addFieldToFilter('agent_id',array('eq'=>$userId));
            if(count($data))    {
                $model['agent_type']=2;
                foreach ($data as $agent) {
                    $about=$agent->getAboutAgent();
                    $image=$agent->getImage();
                }
                $model['about_agent']=$about;
                $model['image']=$image;
            }
        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('adminhtml')->__('Account Information')));
        if ($model->getUserId()) {
            $fieldset->addField('user_id', 'hidden', array(
                'name' => 'user_id',
            ));
        } else {
            if (! $model->hasData('is_active')) {
                $model->setIsActive(1);
            }
        }
            $fieldset->addField('username', 'text', array(
                'name'  => 'username',
                'label' => Mage::helper('adminhtml')->__('User Name'),
                'id'    => 'username',
                'title' => Mage::helper('adminhtml')->__('User Name'),
                'required' => true,
            ));
            $fieldset->addField('firstname', 'text', array(
                'name'  => 'firstname',
                'label' => Mage::helper('adminhtml')->__('First Name'),
                'id'    => 'firstname',
                'title' => Mage::helper('adminhtml')->__('First Name'),
                'required' => true,
            ));

            $fieldset->addField('lastname', 'text', array(
                'name'  => 'lastname',
                'label' => Mage::helper('adminhtml')->__('Last Name'),
                'id'    => 'lastname',
                'title' => Mage::helper('adminhtml')->__('Last Name'),
                'required' => true,
            ));

            $fieldset->addField('email', 'text', array(
                'name'  => 'email',
                'label' => Mage::helper('adminhtml')->__('Email'),
                'id'    => 'customer_email',
                'title' => Mage::helper('adminhtml')->__('User Email'),
                'class' => 'required-entry validate-email',
                'required' => true,
            ));

            $fieldset->addField('current_password', 'obscure', array(
                'name'  => 'current_password',
                'label' => Mage::helper('adminhtml')->__('Current Admin Password'),
                'id'    => 'current_password',
                'title' => Mage::helper('adminhtml')->__('Current Admin Password'),
                'class' => 'input-text',
                'required' => true,
            ));
            if ($model->getUserId()) {
                $fieldset->addField('password', 'password', array(
                    'name'  => 'new_password',
                    'label' => Mage::helper('adminhtml')->__('New Password'),
                    'id'    => 'new_pass',
                    'title' => Mage::helper('adminhtml')->__('New Password'),
                    'class' => 'input-text validate-admin-password',
                ));

                $fieldset->addField('confirmation', 'password', array(
                    'name'  => 'password_confirmation',
                    'label' => Mage::helper('adminhtml')->__('Password Confirmation'),
                    'id'    => 'confirmation',
                    'class' => 'input-text validate-cpassword',
                ));
            }
            else {
               $fieldset->addField('password', 'password', array(
                    'name'  => 'password',
                    'label' => Mage::helper('adminhtml')->__('Password'),
                    'id'    => 'customer_pass',
                    'title' => Mage::helper('adminhtml')->__('Password'),
                    'class' => 'input-text required-entry validate-admin-password',
                    'required' => true,
                ));
               $fieldset->addField('confirmation', 'password', array(
                    'name'  => 'password_confirmation',
                    'label' => Mage::helper('adminhtml')->__('Password Confirmation'),
                    'id'    => 'confirmation',
                    'title' => Mage::helper('adminhtml')->__('Password Confirmation'),
                    'class' => 'input-text required-entry validate-cpassword',
                    'required' => true,
                ));
            }
            if (Mage::getSingleton('admin/session')->getUser()->getId() != $model->getUserId()) {
                $fieldset->addField('is_active', 'select', array(
                    'name'      => 'is_active',
                    'label'     => Mage::helper('adminhtml')->__('This account is'),
                    'id'        => 'is_active',
                    'title'     => Mage::helper('adminhtml')->__('Account Status'),
                    'class'     => 'input-select',
                    'style'        => 'width: 80px',
                    'options'    => array('1' => Mage::helper('adminhtml')->__('Active'), '0' => Mage::helper('adminhtml')->__('Inactive')),
                ));
            }
            $fieldset->addField('user_roles', 'hidden', array(
                'name' => 'user_roles',
                'id'   => '_user_roles',
            ));
            $fieldset->addField("agent_type", "select", array(
    				"label"     =>  Mage::helper("chatsystem")->__("Agent Type"),
    				"name"      =>  "agent_type",
    				'class'     => 'input-select',
                    'id'        =>'agent_type',
                    'style'     => 'width: 80px',
    				'options'   => array('0' => Mage::helper('adminhtml')->__('Admin'), '2' => Mage::helper('adminhtml')->__('Agent')),
    			));
            $fieldset->addField("image", "image", array(
                    "label"     =>  Mage::helper("chatsystem")->__("Image"),
                    "name"      =>  "image",
                    'id'        =>'image',
                    
                ));
            $fieldset->addField("about_agent", "textarea", array(
                    "label"     =>  Mage::helper("chatsystem")->__("About Agent"),
                    "name"      =>  "about_agent",
                    'id'        =>'about_agent',
                    
                ));
		$data = $model->getData();
        unset($data['password']);
        $form->setValues($data);
        $this->setForm($form);
		}
	}
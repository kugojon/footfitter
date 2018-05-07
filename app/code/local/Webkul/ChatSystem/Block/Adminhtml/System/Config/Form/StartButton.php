<?php
/**
 * @category   Webkul
 * @package    Webkul_Chatsystem
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
class Webkul_ChatSystem_Block_Adminhtml_System_Config_Form_StartButton extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('chatsystemadmin/system/config/startButton.phtml');
    }

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->_toHtml();
    }

    public function getAjaxCheckUrl()
    {
        return Mage::helper('adminhtml')->getUrl('adminhtml/settings/startSever');
    }

    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
            'id'        => 'chatsystem_start',
            'label'     => $this->helper('adminhtml')->__('Start Server'),
            'onclick'   => 'javascript:start(); return false;'
        ));
        return $button->toHtml();
    }
}

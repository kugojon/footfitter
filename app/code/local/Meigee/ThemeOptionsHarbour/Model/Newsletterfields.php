<?php 
/**
 * Magento
 *
 * @author    Meigeeteam http://www.meigeeteam.com <nick@meigeeteam.com>
 * @copyright Copyright (C) 2010 - 2014 Meigeeteam
 *
 */
class Meigee_ThemeOptionsHarbour_Model_Newsletterfields
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'1', 'label'=>Mage::helper('ThemeOptionsHarbour')->__('Everywhere except sidebar')),
            array('value'=>'2', 'label'=>Mage::helper('ThemeOptionsHarbour')->__('Only in sidebar')),
			array('value'=>'3', 'label'=>Mage::helper('ThemeOptionsHarbour')->__('Everywhere')),
			array('value'=>'0', 'label'=>Mage::helper('ThemeOptionsHarbour')->__('Hide field'))
        );
    }

}
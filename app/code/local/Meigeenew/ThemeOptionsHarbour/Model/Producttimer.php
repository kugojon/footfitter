<?php 
/**
 * Magento
 *
 * @author    Meigeeteam http://www.meaigeeteam.com <nick@meaigeeteam.com>
 * @copyright Copyright (C) 2010 - 2012 Meigeeteam
 *
 */
class Meigee_ThemeOptionsHarbour_Model_Producttimer
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'0', 'label'=>Mage::helper('ThemeOptionsHarbour')->__('11:11:11:11')),
            array('value'=>'1', 'label'=>Mage::helper('ThemeOptionsHarbour')->__('11d:11h:11m:11s')),
			array('value'=>'2', 'label'=>Mage::helper('ThemeOptionsHarbour')->__('11days:11hours:11minutes:11seconds'))
        );
    }
}
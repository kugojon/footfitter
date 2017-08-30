<?php
/**
 * Magento
 *
 * @author    Meigeeteam http://www.meigeeteam.com <nick@meigeeteam.com>
 * @copyright Copyright (C) 2010 - 2014 Meigeeteam
 *
 */

class Meigee_ThemeOptionsHarbour_Adminhtml_Block_Newsletter_Subscriber_Grid_Renderer_Firstname extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
	{
		// not logged in => use subscriber data
		if ($row->getType() != 2){
			$value = $row->getSubscriberFirstname();
		}
		// logged-in
		else{
			// fallback to customer data if no data found in subscriber
			$value = $row->getSubscriberFirstname() ? $row->getSubscriberFirstname() : $row->getCustomerFirstname();
		}
		
		return $value ? $value : '----';
	}
}
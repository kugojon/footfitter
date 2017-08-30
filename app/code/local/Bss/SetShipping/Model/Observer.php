<?php
class Bss_SetShipping_Model_Observer
{

	public function setShipping(Varien_Event_Observer $observer)
	{
		$matrixrate_104 = 'matrixrate_matrixrate_104';
        $matrixrate_97 = 'matrixrate_matrixrate_97';
        $address = Mage::getModel("checkout/session")->getQuote()->getShippingAddress();
        $quote = Mage::helper('checkout/cart')->getCart()->getQuote();
        $shippingPrices = array();
        foreach($address->getAllShippingRates() as $rate){
            $shippingPrices[$rate->getCode()] = $rate->getPrice();
        }
        if($shippingPrices['matrixrate_matrixrate_104']){
            $quote->getShippingAddress()->setShippingMethod($matrixrate_104);
        }else{
            $quote->getShippingAddress()->setShippingMethod($matrixrate_97);
        }
        $quote->save();
	}
		
}

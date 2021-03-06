<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Cart
 */


class  Amasty_Cart_Block_Bundle_Catalog_Product_Price extends Mage_Bundle_Block_Catalog_Product_Price
{

    protected function _toHtml()
    {
        $html = parent::_toHtml();

        if(Mage::getStoreConfig('amcart/general/enable')){
            $product = $this->getProduct();
            $search = '<div class="price-box">';
            $replace = $search . '<p style="display: none !important" id="amcart-' . $product->getId() .'"></p>';

            $html = str_replace($search, $replace, $html);
        }

        return $html;
    }
}

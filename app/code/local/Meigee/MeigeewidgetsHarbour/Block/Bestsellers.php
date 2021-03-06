<?php
/**
 * Magento
 *
 * @author    Meigeeteam http://www.meaigeeteam.com <nick@meaigeeteam.com>
 * @copyright Copyright (C) 2010 - 2012 Meigeeteam
 *
 */
class Meigee_MeigeewidgetsHarbour_Block_Bestsellers
extends Mage_Catalog_Block_Product_Abstract
implements Mage_Widget_Block_Interface
{
    protected $products;

    protected function _construct() {
        parent::_construct();
    }

    protected function catId()
    {
        $cat = explode("/", $this->getData('featured_category'));     
		return $cat[0];
    }
    public function catName () {
        return Mage::getModel('catalog/category')->load($this->catId());
    }

	 public function getProductsAmount () {
        return $this->getData('products_amount');
    }

    public function getGrid2Description() {
        return $this->getData('grid2_description');
    }

    public function getAddToCart($config) {
		return $this->getData($config);
	}

	public function getProductPrice($config) {
		return $this->getData($config);
	}
	
	public function getProductTimer($config) {
		return $this->getData($config);
	}

	public function getProductName($config) {
		return $this->getData($config);
	}

	public function getQuickView($config) {
		return $this->getData($config);
	}

	public function getWishlist($config) {
		return $this->getData($config);
	}
	
	public function getCompareProducts($config) {
		return $this->getData($config);
	}

	public function getRatingStars($config) {
		return $this->getData($config);
	}
	
	public function getRatingCustLink($config) {
		return $this->getData($config);
	}
	
	public function getRatingAddReviewLink($config) {
		return $this->getData($config);
	}

	public function getProductsPerRow() {
		return $this->getData('products_per_row');
	}

	public function getColumnsRatio(){
		return $this->getData('columns_ratio');
	}

    public function getMyCollection () {
    //     $todayStartOfDayDate  = Mage::app()->getLocale()->date()
    //         ->setTime('00:00:00')
    //         ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

    //     $todayEndOfDayDate  = Mage::app()->getLocale()->date()
    //         ->setTime('23:59:59')
    //         ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

    //     $collection = Mage::getResourceModel('catalog/product_collection');
    //     $collection->setVisibility(Mage::getSingleton('catalog/product_visibility')->getVisibleInCatalogIds());


    //     $collection = $this->_addProductAttributesAndPrices($collection)
    //         ->addStoreFilter()
    //         ->addAttributeToFilter('news_from_date', array('or'=> array(
    //             0 => array('date' => true, 'to' => $todayEndOfDayDate),
    //             1 => array('is' => new Zend_Db_Expr('null')))
    //         ), 'left')
    //         ->addAttributeToFilter('news_to_date', array('or'=> array(
    //             0 => array('date' => true, 'from' => $todayStartOfDayDate),
    //             1 => array('is' => new Zend_Db_Expr('null')))
    //         ), 'left')
    //         ->addAttributeToFilter(
    //             array(
    //                 array('attribute' => 'news_from_date', 'is'=>new Zend_Db_Expr('not null')),
    //                 array('attribute' => 'news_to_date', 'is'=>new Zend_Db_Expr('not null'))
    //                 )
    //           )
    //         ->addAttributeToSort('news_from_date', 'desc')
    //         ->setPageSize($this->getProductsCount())
    //         ->setCurPage(1)
    //     ;

    $collection = Mage::getResourceModel('reports/product_collection')
            ->addAttributeToSelect('*')
            ->addOrderedQty()
            ->setOrder('ordered_qty', 'desc');


        return $collection;
    }
	
    public function getSliderOptions () {
        
         if ($this->getData('template') == 'meigee/meigeewidgetsharbour/slider.phtml' and $this->getData('autoSlide') == 1) {
            $options =
            ', autoSlide: 1, '
            . 'autoSlideTimer:'.$this->getData('autoSlideTimer').','
            .'autoSlideTransTimer:'.$this->getData('autoSlideTransTimer');
			return $options;
		}
    }

    public function getWidgetId () {
        return $this->getData("widget_id");
    }
}

<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_CatalogInventory
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * HTML select element block with customer groups options
 *
 * @category   Mage
 * @package    Mage_CatalogInventory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Ced_Jet_Block_Adminhtml_System_Config_Field_Magentoattribute extends Mage_Core_Block_Html_Select
{

    /**
     * Flag whether to add group all option or no
     *
     * @var bool
     */

    /**
     * Retrieve allowed customer groups
     *
     * @param int $groupId  return name by customer group id
     * @return array|string
     */
    protected function _getMagentoAttributes($groupId = null)
    {


        $attributes = Mage::getResourceModel('catalog/product_attribute_collection')
            ->addFieldToFilter('is_configurable', 1)
            ->addFieldToFilter('is_global', 1)
            ->addFieldToFilter('frontend_input', array('in' => array('select', 'boolean')));




        $arr = array();
        foreach ($attributes as $attribute) {
            $arr[] = array('value' =>$attribute->getAttributecode(), 'label'=>$attribute->getAttributecode());
        }

        return $arr;
    }

    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            foreach ($this->_getMagentoAttributes() as $attribute) {
                $this->addOption($attribute['value'], addslashes($attribute['label']));
            }
        }

        return parent::_toHtml();
    }
}

<?php
/**
 * Magento
 *
 * @author    Meigeeteam http://www.meigeeteam.com <nick@meigeeteam.com>
 * @copyright Copyright (C) 2010 - 2014 Meigeeteam
 *
 */

class Meigee_ThemeOptionsHarbour_Adminhtml_Block_Newsletter_Subscriber_Grid extends Mage_Adminhtml_Block_Newsletter_Subscriber_Grid
{
	protected function _prepareColumns()
	{
		// prepare columns and sort them by order (see Mage_Adminhtml_Block_Widget_Grid)
		parent::_prepareColumns();
		
		// remove old columns
		$this->subsRemoveColumn('firstname');
		$this->subsRemoveColumn('lastname');
		
		
		// add new columns
       $this->addColumnAfter('firstname', array(
			'header'    => Mage::helper('newsletter')->__('First Name'),
            'index'     => 'customer_firstname',
			'renderer'	=> 'Meigee_ThemeOptionsHarbour_Adminhtml_Block_Newsletter_Subscriber_Grid_Renderer_Firstname'
		),'type');
		
		$this->addColumnAfter('lastname', array(
			'header'    => Mage::helper('newsletter')->__('Last Name'),
            'index'     => 'customer_lastname',
			'renderer'	=> 'Meigee_ThemeOptionsHarbour_Adminhtml_Block_Newsletter_Subscriber_Grid_Renderer_Lastname'
		), 'firstname');
		
		// manually sort again, that our custom order works
		$this->sortColumnsByOrder();
		
        return $this;
    }

    /**
     * Wrapper for removeColumn()
     * removeColumn is missing in Magento Professional so we add a fallback;
     *
     * @param string $columnId
     * @return Mediarocks_NewsletterExtended_Adminhtml_Block_Newsletter_Subscriber_Grid
     */
    public function subsRemoveColumn($columnId)
    {
        if (method_exists($this, "removeColumn")){
            return $this->removeColumn($columnId);
        }
        else if(isset($this->_columns[$columnId])){
            unset($this->_columns[$columnId]);
            if ($this->_lastColumnId == $columnId) {
                $this->_lastColumnId = key($this->_columns);
            }
        }
        return $this;
    }
}
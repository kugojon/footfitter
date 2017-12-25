<?php
class Bss_CronAlertProduct_Model_Adminhtml_System_Config_Source_Cron_Frequency extends Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency
{

    protected static $_options;

    const CRON_DAILY    = 'D';
    const CRON_WEEKLY   = 'W';
    const CRON_MONTHLY  = 'M';
    const CRON_5M  = '5M';

    public function toOptionArray()
    {
        if (!self::$_options) {
            self::$_options = array(
                array(
                    'label' => Mage::helper('cron')->__('Daily'),
                    'value' => self::CRON_DAILY,
                ),
                array(
                    'label' => Mage::helper('cron')->__('Weekly'),
                    'value' => self::CRON_WEEKLY,
                ),
                array(
                    'label' => Mage::helper('cron')->__('Monthly'),
                    'value' => self::CRON_MONTHLY,
                ),
                array(
                    'label' => Mage::helper('cron')->__('5 minutes'),
                    'value' => self::CRON_5M,
                ),
            );
        }
        return self::$_options;
    }

}
		
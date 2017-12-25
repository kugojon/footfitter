<?php
class Bss_CronAlertProduct_Model_Adminhtml_System_Config_Backend_Product_Alert_Cron extends Mage_Adminhtml_Model_System_Config_Backend_Product_Alert_Cron
{
    const CRON_STRING_PATH  = 'crontab/jobs/catalog_product_alert/schedule/cron_expr';
    const CRON_MODEL_PATH   = 'crontab/jobs/catalog_product_alert/run/model';

    protected function _afterSave()
    {
        $priceEnable = $this->getData('groups/productalert/fields/allow_price/value');
        $stockEnable = $this->getData('groups/productalert/fields/allow_stock/value');

        $enabled     = $priceEnable || $stockEnable;
        $frequncy    = $this->getData('groups/productalert_cron/fields/frequency/value');
        $time        = $this->getData('groups/productalert_cron/fields/time/value');

        $errorEmail  = $this->getData('groups/productalert_cron/fields/error_email/value');

        $frequencyDaily     = Bss_CronAlertProduct_Model_Adminhtml_System_Config_Source_Cron_Frequency::CRON_DAILY;
        $frequencyWeekly    = Bss_CronAlertProduct_Model_Adminhtml_System_Config_Source_Cron_Frequency::CRON_WEEKLY;
        $frequencyMonthly   = Bss_CronAlertProduct_Model_Adminhtml_System_Config_Source_Cron_Frequency::CRON_MONTHLY;
        $frequency5M   = Bss_CronAlertProduct_Model_Adminhtml_System_Config_Source_Cron_Frequency::CRON_5M;
        $cronDayOfWeek      = date('N');

        if ($frequncy == $frequency5M){
            $cronExprString = '*/5 * * * *';
        }else {
            $cronExprArray      = array(
                intval($time[1]),                                   # Minute
                intval($time[0]),                                   # Hour
                ($frequncy == $frequencyMonthly) ? '1' : '*',       # Day of the Month
                '*',                                                # Month of the Year
                ($frequncy == $frequencyWeekly) ? '1' : '*',         # Day of the Week
            );
            $cronExprString     = join(' ', $cronExprArray);
        }
        

        try {
            Mage::getModel('core/config_data')
                ->load(self::CRON_STRING_PATH, 'path')
                ->setValue($cronExprString)
                ->setPath(self::CRON_STRING_PATH)
                ->save();
            Mage::getModel('core/config_data')
                ->load(self::CRON_MODEL_PATH, 'path')
                ->setValue((string) Mage::getConfig()->getNode(self::CRON_MODEL_PATH))
                ->setPath(self::CRON_MODEL_PATH)
                ->save();
        } catch (Exception $e) {
            throw new Exception(Mage::helper('cron')->__('Unable to save the cron expression.'));
        }
    }
}
		
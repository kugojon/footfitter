<?php
    $installer = $this;
    $installer->startSetup();
    $prefix = Mage::getConfig()->getTablePrefix();
    $connection = $this->getConnection();
    $connection->addColumn($prefix.'wk_cs_conversation', 'to_type', 'varchar(100) NOT NULL');
    $installer->endSetup();
<?php
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer = $this;

$installer->startSetup();


$table = $installer->getConnection()
    ->addColumn($installer->getTable('vr_salesreport/file'),
        'period',
        array(
            "type" => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable' => false,
            'comment' => "Period"
    ));


$installer->endSetup();
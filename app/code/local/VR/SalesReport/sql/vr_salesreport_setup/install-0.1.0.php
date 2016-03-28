<?php
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer = $this;

$installer->startSetup();

if (!$installer->getConnection()->isTableExists($installer->getTable('vr_salesreport/file'))) {
    $table = $installer->getConnection()
        ->newTable($installer->getTable('vr_salesreport/file'))
        ->addColumn('file_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
        ), 'Report Id')
        ->addColumn('filename', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Filename of report');
    $installer->getConnection()->createTable($table);
}
$installer->endSetup();
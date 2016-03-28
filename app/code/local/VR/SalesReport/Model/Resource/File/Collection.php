<?php
/**
 * Created by PhpStorm.
 * User: ilia
 * Date: 25.10.14
 * Time: 16:24
 */ 
class VR_SalesReport_Model_Resource_File_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    protected function _construct()
    {
        $this->_init('vr_salesreport/file');
    }

}
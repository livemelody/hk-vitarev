<?php
/**
 * Created by PhpStorm.
 * User: ilia
 * Date: 25.10.14
 * Time: 16:23
 */ 
class VR_SalesReport_Model_File extends Mage_Core_Model_Abstract
{

    protected function _construct()
    {
        $this->_init('vr_salesreport/file');
    }

    public function loadByPeriod($period)
    {
        $collection = $this->getCollection()->addFieldToFilter("period",array("eq"=>$period));
        if ($collection->getSize()){
            $this->load($collection->getFirstItem()->getId());
        }
        return $this;

    }

}
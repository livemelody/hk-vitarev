<?php

class VR_SalesReport_Block_Adminhtml_Salesreport_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('filesGrid');
        $this->setDefaultSort('file_id');
        $this->setDefaultDir('DESC');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('vr_salesreport/file')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('period', array(
            'header'    => Mage::helper('vr_salesreport')->__('Period'),
            'align'     => 'left',
            'index'     => 'period',
        ));
        $this->addColumn('file_id', array(
            'header'    => Mage::helper('vr_salesreport')->__('File ID'),
            'align'     => 'left',
            'index'     => 'file_id',
        ));


        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/download', array('file_id' => $row->getId()));
    }
}

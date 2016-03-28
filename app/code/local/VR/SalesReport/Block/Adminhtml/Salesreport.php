<?php

class VR_SalesReport_Block_Adminhtml_Salesreport extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {

        $this->_blockGroup = 'vr_salesreport';
        $this->_controller = 'adminhtml_salesreport';
        parent::__construct();
        $this->_headerText = Mage::helper('core')->__('Reports');
        $this->setTemplate('vr/salesreport/container.phtml');
        $this->_removeButton('add');
        $this->addButton('filter_form_submit', array(
            'label'     => Mage::helper('reports')->__('Create Report'),
            'onclick'   => "$('create_report_form').submit()"
        ));

    }

}

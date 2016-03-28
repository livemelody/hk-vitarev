<?php

class NextBits_Clientpo_Block_Adminhtml_Sales_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid
{   
    protected function _prepareMassaction()
    {
        parent::_prepareMassaction();
         
        // Append new mass action option 
        $this->getMassactionBlock()->addItem(
            'newmodule',
            array('label' => $this->__('Print Pro-Forma Invoice'), 
                  'url'   => $this->getUrl('adminhtml/sales_order/proinvoice') //this should be the url where there will be mass operation
            )
        );
    }
}
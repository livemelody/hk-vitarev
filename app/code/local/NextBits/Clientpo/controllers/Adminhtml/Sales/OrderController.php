<?php
require_once 'Mage/Adminhtml/controllers/Sales/OrderController.php';
class NextBits_Clientpo_Adminhtml_Sales_OrderController extends Mage_Adminhtml_Sales_OrderController 
{
	public function proinvoiceAction(){
        $orderIds = $this->getRequest()->getPost('order_ids');
        $flag = false;
        if (!empty($orderIds)) {
            foreach ($orderIds as $orderId) {
                $order = Mage::getModel('sales/order')->load($orderId);
				$customer_id = $order->getCustomerId();
				$customerData = Mage::getModel('customer/customer')->load($customer_id); 
				$custGroupId = $customerData->getGroupId();
				//$invoices = $order->getAllItems();
				if($custGroupId == '2' || $custGroupId == '5' || $custGroupId == '6'){
				   
					$flag = true;
					if (!isset($pdf)){
						$pdf = Mage::getModel('clientpo/proinvoice')->getprePdf($order);
					} else {
						$pages = Mage::getModel('clientpo/proinvoice')->getprePdf($order);
						$pdf->pages = array_merge ($pdf->pages, $pages->pages);
					}
					
				}
			}
            if ($flag) {
                return $this->_prepareDownloadResponse(
                    'pro-invoice'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').'.pdf', $pdf->render(),
                    'application/pdf'
                );
            } else {
                $this->_getSession()->addError($this->__('There are no printable documents related to selected orders or customer is not from wholsale group.'));
                $this->_redirect('*/*/');
            }
		
        }
        $this->_redirect('*/*/');
    }

}
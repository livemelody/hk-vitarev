<?php
class NextBits_Clientpo_Model_Observer extends Varien_Object
{

   public function saveClient(Varien_Event_Observer $observer)
   {
	   if (Mage::app()->getStore()->isAdmin()) {
		$dara = Mage::app()->getRequest()->getParam('order');
		$order = $observer->getEvent()->getOrder();
		$order->setClientPo($dara['client']['client_po']);
		$order->setCustomOrderId($dara['client']['order_ids']);

     
	   }
   }
}
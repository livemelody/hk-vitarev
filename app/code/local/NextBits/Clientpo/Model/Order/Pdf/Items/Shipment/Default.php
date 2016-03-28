<?php
class NextBits_Clientpo_Model_Order_Pdf_Items_Shipment_Default extends Mage_Sales_Model_Order_Pdf_Items_Abstract
{
    /**
     * Draw item line
     */
    public function draw()
    {
		$order  = $this->getOrder();
        $item   = $this->getItem();
		$pdf    = $this->getPdf();
        $page   = $this->getPage();
        $lines  = array();
		
		
		$flag = Mage::registry('pro_flags');
		
		// $orderItems = $order->getItemsCollection();
		// $oqty = 0;//$item->getQty();
		// foreach ($orderItems as $item1){
			// if($item1->getId() == $item->getOrderItemId()){
				// $oqty = $item1->getQtyOrdered();
			// }
		// }
        $prod = Mage::getModel('catalog/product')->load($item->getProductId());
        
		$customer_id = $order->getCustomerId();
		$customerData = Mage::getModel('customer/customer')->load($customer_id); 
		$custGroupId = $customerData->getGroupId();
		if($custGroupId == '2' || $custGroupId == '5' || $custGroupId == '6'){

			$lines[0] = array(array(
				'text' => Mage::helper('core/string')->str_split($prod->getUpc(), 35),
				'feed' => 35,
				//'align' => 'right'
			));

			// draw SKU
			$lines[0][] = array(
				'text'  => Mage::helper('core/string')->str_split($this->getSku($item,$flag), 17),
				'feed'  => 110,
				
			);
			
			
			$lines[0][] = array(
				'text'  => Mage::helper('core/string')->str_split(str_replace(array("&reg;","®"), array("R","R"),$item->getName()), 60, true, true),
				'feed'  => 165,
				
			);
			
			// draw QTY
			$lines[0][] = array(
				'text'  => (int)$item->getQtyOrdered(),
				'feed'  => 495,
				'align' => 'right'
			);
			
			// draw QTY
			$lines[0][] = array(
				'text'  => (int)$item->getQtyShipped() * 1,
				'feed'  => 565,
				'align' => 'right'
			);

			// draw item Prices
			
		}else{
			
			 $lines[0] = array(array(
            'text' => Mage::helper('core/string')->str_split($item->getName(), 60, true, true),
            'feed' => 100,
			));

			// draw QTY
			$lines[0][] = array(
				'text'  => $item->getQty()*1,
				'feed'  => 35
			);

			// draw SKU
			$lines[0][] = array(
				'text'  => Mage::helper('core/string')->str_split($this->getSku($item), 25),
				'feed'  => 565,
				'align' => 'right'
			);
			
		}

        // Custom options
        $options = $this->getItemOptions();
        if ($options) {
            foreach ($options as $option) {
                // draw options label
                $lines[][] = array(
                    'text' => Mage::helper('core/string')->str_split(strip_tags($option['label']), 70, true, true),
                    'font' => 'italic',
                    'feed' => 110
                );

                // draw options value
                if ($option['value']) {
                    $_printValue = isset($option['print_value'])
                        ? $option['print_value']
                        : strip_tags($option['value']);
                    $values = explode(', ', $_printValue);
                    foreach ($values as $value) {
                        $lines[][] = array(
                            'text' => Mage::helper('core/string')->str_split($value, 50, true, true),
                            'feed' => 115
                        );
                    }
                }
            }
        }

        $lineBlock = array(
            'lines'  => $lines,
            'height' => 20
        );

        $page = $pdf->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
        $this->setPage($page);
    }
	
	  public function getSku($item,$flag)
    {	
		if($flag == 1){
			if ($item->getProductOptionByCode('simple_sku'))
				return $item->getProductOptionByCode('simple_sku');
			else
				return $item->getSku();
		}else{
			if ($item->getOrderItem()->getProductOptionByCode('simple_sku'))
				return $item->getOrderItem()->getProductOptionByCode('simple_sku');
			else
				return $item->getSku();
		}
       
    }
	
	public function getItemOptions() {
        $result = array();
		$flag = Mage::registry('pro_flags');
		if($flag != ''){
			$options = $this->getItem()->getProductOptions();
		}else{
			$options = $this->getItem()->getOrderItem()->getProductOptions();
		}
        if ($options) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }
        }
        return $result;
    } 
}
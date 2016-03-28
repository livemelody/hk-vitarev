<?php
class NextBits_Clientpo_Model_Order_Pdf_Items_Invoice_Default extends Mage_Sales_Model_Order_Pdf_Items_Abstract
{
    /**
     * Draw item line
     */
    public function draw()
    {
        $order  = $this->getOrder();
        $item   = $this->getItem();
		$prod = Mage::getModel('catalog/product')->load($item->getProductId());
        $pdf    = $this->getPdf();
        $page   = $this->getPage();
        $lines  = array();
		
		$customer_id = $order->getCustomerId();
		$flag = Mage::registry('pro_flags');
		if($flag != ''){
			$sku = $this->getSku($item,$flag);
			$qty = $item->getQtyOrdered();
		}else{
			$flag = 0;
			$sku = $this->getSku($item,$flag);
			$qty = $item->getQty();
		}
		
		$customerData = Mage::getModel('customer/customer')->load($customer_id); 
		$custGroupId = $customerData->getGroupId();
		if($custGroupId == '2' || $custGroupId == '5' || $custGroupId == '6'){
			
			 // draw Product name
			$lines[0] = array(array(
				'text' => Mage::helper('core/string')->str_split($prod->getUpc(), 35),
				'feed' => 35,
				//'align' => 'right'
			));

			// draw SKU
			$lines[0][] = array(
				'text'  => Mage::helper('core/string')->str_split($sku, 17),
				'feed'  => 110,
				
			);
			
			
			$lines[0][] = array(
				'text'  => Mage::helper('core/string')->str_split(str_replace(array("&reg;","®"), array("R","R"),$item->getName()), 60, true, true),
				'feed'  => 165,
				
			);

			// draw QTY
			$lines[0][] = array(
				'text'  => $qty * 1,
				'feed'  => 455,
				'align' => 'right'
			);

			// draw item Prices
			$i = 0;
			$prices = $this->getItemPricesForDisplay();
			
			$feedPrice = 505;
			$feedSubtotal = $feedPrice + 60;
			foreach ($prices as $priceData){
				if (isset($priceData['label'])) {
					// draw Price label
					$lines[$i][] = array(
						'text'  => $priceData['label'],
						'feed'  => $feedPrice,
						'align' => 'right'
					);
					// draw Subtotal label
					$lines[$i][] = array(
						'text'  => $priceData['label'],
						'feed'  => $feedSubtotal,
						'align' => 'right'
					);
					$i++;
				}
				// draw Price
				$lines[$i][] = array(
					'text'  => $priceData['price'],
					'feed'  => $feedPrice,
					'font'  => 'bold',
					'align' => 'right'
				);
				// draw Subtotal
				$lines[$i][] = array(
					'text'  => $priceData['subtotal'],
					'feed'  => $feedSubtotal,
					'font'  => 'bold',
					'align' => 'right'
				);
				$i++;
			}

			
			
		}else{
			
			 // draw Product name
			$lines[0] = array(array(
				'text' => Mage::helper('core/string')->str_split($item->getName(), 35, true, true),
				'feed' => 35,
			));

			// draw SKU
			$lines[0][] = array(
				'text'  => Mage::helper('core/string')->str_split($this->getSku($item), 17),
				'feed'  => 290,
				'align' => 'right'
			);

			// draw QTY
			$lines[0][] = array(
				'text'  => $item->getQty() * 1,
				'feed'  => 435,
				'align' => 'right'
			);

			// draw item Prices
			$i = 0;
			$prices = $this->getItemPricesForDisplay();
			$feedPrice = 395;
			$feedSubtotal = $feedPrice + 170;
			foreach ($prices as $priceData){
				if (isset($priceData['label'])) {
					// draw Price label
					$lines[$i][] = array(
						'text'  => $priceData['label'],
						'feed'  => $feedPrice,
						'align' => 'right'
					);
					// draw Subtotal label
					$lines[$i][] = array(
						'text'  => $priceData['label'],
						'feed'  => $feedSubtotal,
						'align' => 'right'
					);
					$i++;
				}
				// draw Price
				$lines[$i][] = array(
					'text'  => $priceData['price'],
					'feed'  => $feedPrice,
					'font'  => 'bold',
					'align' => 'right'
				);
				// draw Subtotal
				$lines[$i][] = array(
					'text'  => $priceData['subtotal'],
					'feed'  => $feedSubtotal,
					'font'  => 'bold',
					'align' => 'right'
				);
				$i++;
			}

			// draw Tax
			$lines[0][] = array(
				'text'  => $order->formatPriceTxt($item->getTaxAmount()),
				'feed'  => 495,
				'font'  => 'bold',
				'align' => 'right'
			);

			
			
		}
       
	   // custom options
			$options = $this->getItemOptions();
			if ($options) {
				foreach ($options as $option) {
					// draw options label
					$lines[][] = array(
						'text' => Mage::helper('core/string')->str_split(strip_tags($option['label']), 40, true, true),
						'font' => 'italic',
						'feed' => 35
					);

					if ($option['value']) {
						if (isset($option['print_value'])) {
							$_printValue = $option['print_value'];
						} else {
							$_printValue = strip_tags($option['value']);
						}
						$values = explode(', ', $_printValue);
						foreach ($values as $value) {
							$lines[][] = array(
								'text' => Mage::helper('core/string')->str_split($value, 30, true, true),
								'feed' => 40
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

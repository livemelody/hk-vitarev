<?php
class NextBits_Clientpo_Model_Order_Pdf_Shipment extends Mage_Sales_Model_Order_Pdf_Shipment
{

	public $pagenum = 1;
	public function newPage(array $settings = array())
    {
			
        /* Add new table head */
        $page = $this->_getPdf()->newPage(Zend_Pdf_Page::SIZE_A4);
        $this->_getPdf()->pages[] = $page;
        $this->y = 800;
        if (!empty($settings['table_header'])) {
			$flag = Mage::registry('pro_flags');
			if($flag == 1){
				 $this->_drawHeaders($page,$flag);
			
			}else{
				$this->_drawHeader($page);
			}
           
        }
		$GLOBALS['pagenum'] = $GLOBALS['pagenum']+1;
		if(Mage::registry('ship_footer') == 1){
			
			$this->_drawFooter($page);
		}
        return $page;
    }
	
	
	 public function getPdf($shipments = array())
	{
		
        $this->_beforeGetPdf();
        $this->_initRenderer('shipment');

        $pdf = new Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new Zend_Pdf_Style();
        $this->_setFontBold($style, 10);
        foreach ($shipments as $shipment) {
            if ($shipment->getStoreId()) {
                Mage::app()->getLocale()->emulate($shipment->getStoreId());
                Mage::app()->setCurrentStore($shipment->getStoreId());
            }
			$orders = Mage::getModel('sales/order')->load($shipment->getOrderId());
			$customer_id = $orders->getCustomerId();
			$customerData = Mage::getModel('customer/customer')->load($customer_id); 
			$custGroupId = $customerData->getGroupId();
			if($custGroupId == '2' || $custGroupId == '5' || $custGroupId == '6'){
				 $page  = $this->newPage();
				$order = $shipment->getOrder();
			/* Add image */
				$this->insertLogoWf($page, $shipment->getStore());
				/* Add address */
				$this->insertAddressWs($page, $shipment->getStore());
				/* Add head */
				$this->insertOrderWf(
					$page,
					$shipment,
					Mage::getStoreConfigFlag(self::XML_PATH_SALES_PDF_SHIPMENT_PUT_ORDER_ID, $shipment->getStoreId())
				);
				/* Add document text and number */
				$this->insertDocumentNumber(
					$page,
					Mage::helper('sales')->__('Packingslip # ') . $shipment->getIncrementId()
				);
				/* Add table */
				Mage::register('ship_footer',1);
				$this->_drawFooter($page);
			
				$this->_drawHeaders($page);
				/* Add body */
				$totalOrder = 0;
				$totalShipped = 0;
				foreach ($order->getAllItems() as $item) {
					$totalShipped = $totalShipped + $item->getQtyShipped();
					$totalOrder = $totalOrder + $item->getQtyOrdered();
					if ($item->getParentItem()) {
						
						continue;
					}
					/* Draw item */
					$this->_drawItemWsship($item, $page, $shipment);
					$page = end($pdf->pages);
				}
				
				$this->y = 	$this->y-20;		
				if($this->y < 60){
					$page  = $this->newPage();
				}
				$page->setFillColor(new Zend_Pdf_Color_Rgb(255, 255, 255));
				$page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
				$page->setLineWidth(0.5);
				$page->drawRectangle(340, $this->y, 570, $this->y-25);
				$page->setFillColor(new Zend_Pdf_Color_Rgb(0, 0, 0));
				$this->_setFontBold($page, 9);
				$page->drawText('Total Quantity: ', 345, $this->y-15, 'UTF-8');
				$this->_setFontRegular($page, 9);
				$page->drawText((int)$totalShipped, 555, $this->y-15, 'UTF-8');
				$this->_setFontRegular($page, 9);	
				$page->drawText((int)$totalOrder, 485, $this->y-15, 'UTF-8');
				$page->drawLine(435, $this->y, 435, $this->y - 25);
				$page->drawLine(505, $this->y, 505, $this->y - 25);
				$this->y = $this->y-70;
				if($this->y < 60){ 	
					$page  = $this->newPage();
				}   
				$this->_setFontRegular($page, 12); 
				$page->drawText('Thank you and best regards!', 220, $this->y, 'UTF-8');
				
				Mage::unregister('ship_footer');
			}else{
				
			
            $page  = $this->newPage();
            $order = $shipment->getOrder();
            /* Add image */
            $this->insertLogo($page, $shipment->getStore());
            /* Add address */
            $this->insertAddress($page, $shipment->getStore());
            /* Add head */
            $this->insertOrder(
                $page,
                $shipment,
                Mage::getStoreConfigFlag(self::XML_PATH_SALES_PDF_SHIPMENT_PUT_ORDER_ID, $order->getStoreId())
            );
            /* Add document text and number */
            $this->insertDocumentNumber(
                $page,
                Mage::helper('sales')->__('Packingslip # ') . $shipment->getIncrementId()
            );
            /* Add table */
            $this->_drawHeader($page);
            /* Add body */
            foreach ($shipment->getAllItems() as $item) {
                if ($item->getOrderItem()->getParentItem()) {
                    continue;
                }
                /* Draw item */
                $this->_drawItem($item, $page, $order);
                $page = end($pdf->pages);
            }
        }
		}
        $this->_afterGetPdf();
        if ($shipment->getStoreId()) {
            Mage::app()->getLocale()->revert();
        }
        return $pdf;
    }
	
	protected function _drawFooter(Zend_Pdf_Page $page)
    {
        /* Add table foot */
	
        $this->_setFontRegular($page, 12);
        $page->setFillColor(new Zend_Pdf_Color_Rgb(255, 255, 255));
		$page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(2.5);
		$page->drawRectangle(25, $page->getHeight()-810, 570, $page->getHeight()-820);
		$page->setFillColor(new Zend_Pdf_Color_Rgb(0, 0, 0));
		$this->_setFontRegular($page, 9);
		$page->drawText("Page No. ".$GLOBALS['pagenum'], 525, $page->getHeight()-830, 'UTF-8');
  
    }
	
	protected function _drawItemWsship(Varien_Object $item, Zend_Pdf_Page $page, Mage_Sales_Model_Order $order ,Zend_Pdf $pdf)
    {
       // $orderItem = $item->getOrderItem();
		Mage::register('pro_flags',1);
        $type = $item->getProductType();
        $renderer = $this->_getRenderer($type);
		
		$this->_setFontRegular($page, 12);
		$this->renderItem($item, $page, $order, $renderer);
        //$this->draw($item, $page, $order, $pdf);
		
         $transportObject = new Varien_Object(array('renderer_type_list' => array()));
         Mage::dispatchEvent('pdf_item_draw_after', array(
            'transport_object' => $transportObject,
            'entity_item'      => $item
        ));  
		
         foreach ($transportObject->getRendererTypeList() as $type) {
            $renderer = $this->_getRenderer($type);
            if ($renderer) {
                $this->renderItem($item, $page, $order, $renderer);
            }
        } 
		Mage::unregister('pro_flags');
        return $renderer->getPage();
    }
	
	
	
	
	public function _drawHeaders(Zend_Pdf_Page $page)
    {
        /* Add table head */
       $this->_setFontRegular($page, 12);
        $page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
        $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $this->y, 570, $this->y -15);
        $this->y -= 10;
        $page->setFillColor(new Zend_Pdf_Color_RGB(0, 0, 0));

        //columns headers
        $lines[0][] = array(
            'text' => Mage::helper('sales')->__('UPC'),
            'feed' => 35
        );

        $lines[0][] = array(
            'text'  => Mage::helper('sales')->__('SKU'),
            'feed'  => 110,
           // 'align' => 'right'
        );

        $lines[0][] = array(
            'text'  => Mage::helper('sales')->__('Description'),
            'feed'  => 165,
           // 'align' => 'right'
        );
		
		 $lines[0][] = array(
            'text'  => Mage::helper('sales')->__('Items Ordered'),
            'feed'  => 495,
            'align' => 'right'
        );
		
        $lines[0][] = array(
            'text'  => Mage::helper('sales')->__('Items Shipped'),
            'feed'  => 565,
            'align' => 'right'
        );

      /*   $lines[0][] = array(
            'text'  => Mage::helper('sales')->__('Unit Price'),
            'feed'  => 495,
            'align' => 'right'
        );

        $lines[0][] = array(
            'text'  => Mage::helper('sales')->__('Total'),
            'feed'  => 565,
            'align' => 'right'
        ); */

        $lineBlock = array(
            'lines'  => $lines,
            'height' => 5
        );

        $this->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->y -= 20;
		
    }
	
	public function drawLineBlocks(Zend_Pdf_Page $page, array $draw, array $pageSettings = array())
    {
        foreach ($draw as $itemsProp) {
            if (!isset($itemsProp['lines']) || !is_array($itemsProp['lines'])) {
                Mage::throwException(Mage::helper('sales')->__('Invalid draw line data. Please define "lines" array.'));
            }
            $lines  = $itemsProp['lines'];
            $height = isset($itemsProp['height']) ? $itemsProp['height'] : 10;

            if (empty($itemsProp['shift'])) {
                $shift = 0;
                foreach ($lines as $line) {
                    $maxHeight = 0;
                    foreach ($line as $column) {
                        $lineSpacing = !empty($column['height']) ? $column['height'] : $height;
                        if (!is_array($column['text'])) {
                            $column['text'] = array($column['text']);
                        }
                        $top = 0;
                        foreach ($column['text'] as $part) {
                            $top += $lineSpacing;
                        }

                        $maxHeight = $top > $maxHeight ? $top : $maxHeight;
                    }
                    $shift += $maxHeight;
                }
                $itemsProp['shift'] = $shift;
            }

            if ($this->y - $itemsProp['shift'] < 25) {
                $page = $this->newPage($pageSettings);
            }

            foreach ($lines as $line) {
                $maxHeight = 0;
                foreach ($line as $column) {
                    $fontSize = empty($column['font_size']) ? 10 : $column['font_size'];
                    if (!empty($column['font_file'])) {
                        $font = Zend_Pdf_Font::fontWithPath($column['font_file']);
                        $page->setFont($font, $fontSize);
                    } else {
                        $fontStyle = empty($column['font']) ? 'regular' : $column['font'];
                        switch ($fontStyle) {
                            case 'bold':
                                $font = $this->_setFontBold($page, $fontSize);
                                break;
                            case 'italic':
                                $font = $this->_setFontItalic($page, $fontSize);
                                break;
                            default:
                                $font = $this->_setFontRegular($page, $fontSize);
                                break;
                        }
                    }

                    if (!is_array($column['text'])) {
                        $column['text'] = array($column['text']);
                    }

                    $lineSpacing = !empty($column['height']) ? $column['height'] : $height;
                    $top = 0;
                    foreach ($column['text'] as $part) {
                        if ($this->y - $lineSpacing < 25) {
                            $page = $this->newPage($pageSettings);
                        }

                        $feed = $column['feed'];
                        $textAlign = empty($column['align']) ? 'left' : $column['align'];
                        $width = empty($column['width']) ? 0 : $column['width'];
                        switch ($textAlign) {
                            case 'right':
                                if ($width) {
                                    $feed = $this->getAlignRight($part, $feed, $width, $font, $fontSize);
                                }
                                else {
                                    $feed = $feed - $this->widthForStringUsingFontSize($part, $font, $fontSize);
                                }
                                break;
                            case 'center':
                                if ($width) {
                                    $feed = $this->getAlignCenter($part, $feed, $width, $font, $fontSize);
                                }
                                break;
                        }
                        $page->drawText($part, $feed, $this->y-$top, 'UTF-8');
                        $top += $lineSpacing;
                    }

                    $maxHeight = $top > $maxHeight ? $top : $maxHeight;
                }
                $this->y -= $maxHeight;
            }
        }

        return $page;
    }

	
	protected function insertLogoWf(&$page, $store = null)
    {
			
        $this->y = $this->y ? $this->y : 815;
		
		$page->setFillColor(new Zend_Pdf_Color_Rgb(255, 255, 255));
		$page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(2.5);
		$page->drawRectangle(25, $this->y+30, 580, 755);
		
		$page->setFillColor(new Zend_Pdf_Color_Rgb(255, 255, 255));
		$page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
		$page->drawRectangle(30, $this->y+25, 575, 760); 
		$page->drawLine(352, $this->y+25, 352, $this->y - 40);		
		
        $image = Mage::getStoreConfig('sales/identity/logo_ws', $store);;
        if ($image) {
             $image = Mage::getBaseDir('media') . '/sales/store/logo/' . $image;
            if (is_file($image)) {
                $image       = Zend_Pdf_Image::imageWithPath($image);
                $top         = 818; //top border of the page
                $widthLimit  = 270; //half of the page width
                $heightLimit = 270; //assuming the image is not a "skyscraper"
                $width       = $image->getPixelWidth();
                $height      = $image->getPixelHeight();

                //preserving aspect ratio (proportions)
                $ratio = $width / $height;
                if ($ratio > 1 && $width > $widthLimit) {
                    $width  = $widthLimit;
                    $height = $width / $ratio;
                } elseif ($ratio < 1 && $height > $heightLimit) {
                    $height = $heightLimit;
                    $width  = $height * $ratio;
                } elseif ($ratio == 1 && $height > $heightLimit) {
                    $height = $heightLimit;
                    $width  = $widthLimit;
                }

                $y1 = $top - $height;
                $y2 = $top;
                $x1 = 45;
                $x2 = $x1 + $width;
				
                //coordinates after transformation are rounded by Zend
                $page->drawImage($image, $x1, $y1, $x2, $y2);

                $this->y = $y1 - 10;
            }
        }
    }
	
	 protected function insertAddressWs(&$page, $store = null)
    {
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $font = $this->_setFontRegular($page, 9);
        $page->setLineWidth(0);
        $this->y = $this->y ? $this->y : 815;
        $top = 815;
        foreach (explode("\n", Mage::getStoreConfig('sales/identity/address_ws', $store)) as $value){
            if ($value !== '') {
                $value = preg_replace('/<br[^>]*>/i', "\n", $value);
                foreach (Mage::helper('core/string')->str_split($value, 45, true, true) as $_value) {
                    $page->drawText(trim(strip_tags($_value)),
                        360,
                        $top,
                        'UTF-8');
                    $top -= 10;
                }
            }
        }
        $this->y = ($this->y > $top) ? $top : $this->y;
    }
	
	public function insertOrderWf(&$page, $obj, $putOrderId = true)
    {
        if ($obj instanceof Mage_Sales_Model_Order) {
            $shipment = null;
            $order = $obj;
        } elseif ($obj instanceof Mage_Sales_Model_Order_Shipment) {
            $shipment = $obj;
            $order = $shipment->getOrder();
        }
		
		$orderData = $order->getData();
		
        $this->y = $this->y ? $this->y : 815;
        $top = $this->y;
        $top -= 20;
		$page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->_setFontBold($page, 22);
		$page->drawText('Packing List', 460, ($top - 15), 'UTF-8');
      //  $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
        $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
       // $page->drawRectangle(25, $top, 275, ($top - 25));
       // $page->drawRectangle(275, $top, 570, ($top - 25));

        /* Calculate blocks info */

        /* Billing Address */
        $billingAddress = $this->_formatAddress($order->getBillingAddress()->format('pdf'));

        /* Payment */
        $paymentInfo = Mage::helper('payment')->getInfoBlock($order->getPayment())
            ->setIsSecureMode(true)
            ->toPdf();
        $paymentInfo = htmlspecialchars_decode($paymentInfo, ENT_QUOTES);
        $payment = explode('{{pdf_row_separator}}', $paymentInfo);
        foreach ($payment as $key=>$value){
            if (strip_tags(trim($value)) == '') {
                unset($payment[$key]);
            }
        }
        reset($payment);

        /* Shipping Address and Method */
        if (!$order->getIsVirtual()) {
            /* Shipping Address */
            $shippingAddress = $this->_formatAddress($order->getShippingAddress()->format('pdf'));
            $shippingMethod  = $order->getShippingDescription();
        }

       $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
       $this->_setFontRegular($page, 12);
        $page->drawText(Mage::helper('sales')->__('Bill to:'), 35, ($top - 40), 'UTF-8');
		$page->drawText(Mage::helper('sales')->__('Invoice No.:'), 300, ($top - 40), 'UTF-8');
		$page->drawText(Mage::helper('sales')->__('Customer ID'), 300, ($top - 60), 'UTF-8');
		

        $addressesHeight = $this->_calcAddressHeight($billingAddress);
        if (isset($shippingAddress)) {
            $addressesHeight = max($addressesHeight, $this->_calcAddressHeight($shippingAddress));
        }

        $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
       // $page->drawRectangle(25, ($top - 25), 275, $top - 33 - $addressesHeight);
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->_setFontRegular($page, 12);
        $this->y = $top - 60;
        $addressesStartY = $this->y;

        foreach ($billingAddress as $value){
            if ($value !== '') {
                $text = array();
                foreach (Mage::helper('core/string')->str_split($value, 45, true, true) as $_value) {
                    $text[] = $_value;
                }
                foreach ($text as $part) {
                    $page->drawText(strip_tags(ltrim($part)), 35, $this->y, 'UTF-8');
                    $this->y -= 15;
                }
            }
        }
		$page->drawText($orderData['custom_order_id'], 400, ($top - 40), 'UTF-8');
		$page->drawText($orderData['customer_id'], 400, ($top - 60), 'UTF-8');

        $addressesEndY = $this->y;

       
            $addressesEndY = min($addressesEndY, $this->y);
            $this->y = $addressesEndY;
			$this->y -= 20;
			
			$x = 35;
			
			$this->_setFontRegular($page, 12);
			$page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
			$page->drawRectangle(25, $this->y - 3, $page->getWidth()-25, $this->y + 12);
			$page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
			$page->drawText('Date ', $x + 30, $this->y+2, 'UTF-8');
			$page->drawText('Order No.', $x + 170, $this->y+2, 'UTF-8');
			$page->drawText('Terms', $x + 410, $this->y+2, 'UTF-8');
			 
			$page->drawLine($x + 90, $this->y + 12, $x + 90, $this->y - 8);
			
			$page->drawLine($x + 320, $this->y + 12, $x + 320, $this->y - 8);
			 
			$this->y -= 15;
			$this->_setFontRegular($page, 12);
			 
			$page->setLineWidth(0.5);
			$page->drawRectangle(25, $this->y - 20, $page->getWidth()-25, $this->y + 12, Zend_Pdf_Page::SHAPE_DRAW_STROKE);
			//$page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
             //$page->setLineWidth(0.5);
			//$page->drawRectangle(35, 560, 560, 475);
			$page->drawLine($x + 90, $this->y + 12, $x + 90, $this->y - 20);
			$page->drawLine($x + 320, $this->y + 12, $x + 320, $this->y - 20);
			$page->drawText(Mage::helper('core')->formatDate($orderData['created_at'], 'medium'), 36, $this->y, 'UTF-8');
		
			

			$page->drawText("Order # ".$orderData['increment_id'], $x + 145, $this->y, 'UTF-8');
			if($orderData['client_po']){
				$page->drawText("Client Ref PO # ".$orderData['client_po'], $x + 145, $this->y-15, 'UTF-8');
			}
		
			
			
			$page->drawText("Payment Upon Receipt", $x + 375, $this->y, 'UTF-8');
			$this->y -= 50;
			
		
			

            $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
    }

}  
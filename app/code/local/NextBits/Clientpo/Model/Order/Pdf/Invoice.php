<?php
class NextBits_Clientpo_Model_Order_Pdf_Invoice extends Mage_Sales_Model_Order_Pdf_Invoice
{
 public function getPdf($invoices = array())
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('invoice');

        $pdf = new Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new Zend_Pdf_Style();
        $this->_setFontBold($style, 10);

        foreach ($invoices as $invoice) {
			if ($invoice->getStoreId()) {
                Mage::app()->getLocale()->emulate($invoice->getStoreId());
                Mage::app()->setCurrentStore($invoice->getStoreId());
            }
			$orders = Mage::getModel('sales/order')->load($invoice->getOrderId());
				$customer_id = $orders->getCustomerId();
				$customerData = Mage::getModel('customer/customer')->load($customer_id); 
				$custGroupId = $customerData->getGroupId();
			// if($custGroupId == '2' || $custGroupId == '5' || $custGroupId == '6'){
				 
				// $page  = $this->newPage();
				// $order = $invoice->getOrder();
				// /* Add image */
				// $this->insertLogos($page, $invoice->getStore());
				// /* Add address */
				// $this->insertAddressWs($page, $invoice->getStore());
				// /* Add head */
				// $this->insertOrderWf(
					// $page,
					// $order,
					// Mage::getStoreConfigFlag(self::XML_PATH_SALES_PDF_INVOICE_PUT_ORDER_ID, $order->getStoreId())
				// );
				// /* Add document text and number */
				// $this->insertDocumentNumber(
					// $page,
					// Mage::helper('sales')->__('Invoice # ') . $invoice->getIncrementId()
				// );
				// /* Add table */
				// $this->_drawHeaders($page);
				// /* Add body */
				// foreach ($invoice->getAllItems() as $item){
					// if ($item->getOrderItem()->getParentItem()) {
						// continue;
					// }
					// /* Draw item */
					
					// $this->_drawItemWs($item, $page, $order);
					// $page = end($pdf->pages);
				// }
				// /* Add totals */
				// $this->insertTotalsWs($page, $invoice);
				// if ($invoice->getStoreId()) {
					// Mage::app()->getLocale()->revert();
				// }
				// $this->_drawFooter($page); 
			// }else{
				
				$page  = $this->newPage();
				$order = $invoice->getOrder();
				/* Add image */
				$this->insertLogo($page, $invoice->getStore());
				/* Add address */
				$this->insertAddress($page, $invoice->getStore());
				/* Add head */
				$this->insertOrder(
					$page,
					$order,
					Mage::getStoreConfigFlag(self::XML_PATH_SALES_PDF_INVOICE_PUT_ORDER_ID, $order->getStoreId())
				);
				/* Add document text and number */
				$this->insertDocumentNumber(
					$page,
					Mage::helper('sales')->__('Invoice # ') . $invoice->getIncrementId()
				);
				/* Add table */
				$this->_drawHeader($page);
				/* Add body */
				foreach ($invoice->getAllItems() as $item){
					if ($item->getOrderItem()->getParentItem()) {
						continue;
					}
					/* Draw item */
					$this->_drawItem($item, $page, $order);
					$page = end($pdf->pages);
				}
				/* Add totals */
				$this->insertTotals($page, $invoice);
				if ($invoice->getStoreId()) {
					Mage::app()->getLocale()->revert();
				}
			// }
           
        }
        $this->_afterGetPdf();
        return $pdf;
    }
	
	/* protected function _drawFooter(Zend_Pdf_Page $page)
    {
        /* Add table foot 
        $this->_setFontRegular($page, 12);
        $this->y -= 60;
        $page->setFillColor(new Zend_Pdf_Color_RGB(255, 255, 255));
		
		$page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(2.5);
		$page->drawRectangle($page->getHeight()-770, 570, $page->getHeight()-780);
		  // $page->drawLine(25, $this->y-20, 570,$this->y-20 );
		  // $page->drawLine(25, $this->y-30, 570, $this->y-30);
  
    } */
	
	
	
	
	protected function _drawItemWs(Varien_Object $item, Zend_Pdf_Page $page, Mage_Sales_Model_Order $order)
    {
        $orderItem = $item->getOrderItem();
        $type = $orderItem->getProductType();
        $renderer = $this->_getRenderer($type);
		$this->_setFontRegular($page, 12);
        $this->renderItem($item, $page, $order, $renderer);
		
        $transportObject = new Varien_Object(array('renderer_type_list' => array()));
         Mage::dispatchEvent('pdf_item_draw_after', array(
            'transport_object' => $transportObject,
            'entity_item'      => $item
        )); 
		
        foreach ($transportObject->getRendererTypeList() as $type) {
            $renderer = $this->_getRenderer($type);
            if ($renderer) {
                $this->renderItem($orderItem, $page, $order, $renderer);
            }
        }

        return $renderer->getPage();
    }
	
	protected function insertTotalsWs($page, $source){
        $order = $source->getOrder();
        $totals = $this->_getTotalsList($source);
        $lineBlock = array(
            'lines'  => array(),
            'height' => 15
        );
        foreach ($totals as $total) {
            $total->setOrder($order)
                ->setSource($source);

            if ($total->canDisplay()) {
                $total->setFontSize(12);
                foreach ($total->getTotalsForDisplay() as $totalData) {
                    $lineBlock['lines'][] = array(
                        array(
                            'text'      => $totalData['label'],
                            'feed'      => 475,
                            'align'     => 'right',
                            'font_size' => $totalData['font_size'],
                            'font'      => 'bold'
                        ),
                        array(
                            'text'      => $totalData['amount'],
                            'feed'      => 565,
                            'align'     => 'right',
                            'font_size' => $totalData['font_size'],
                            'font'      => 'bold'
                        ),
                    );
                }
            }
        }

        $this->y -= 20;
        $page = $this->drawLineBlocks($page, array($lineBlock));
		$page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->_setFontRegular($page, 12);
		$page->drawText('Please kindly make payment for this Invoice to:', 35, $this->y	, 'UTF-8');
		 $this->_setFontRegular($page, 12);
		$page->drawText('HSBC Account # ', 85, $this->y-30, 'UTF-8');
		 $this->_setFontBold($page, 12);
		$page->drawText('848 451910 838', 175, $this->y-30, 'UTF-8');
		$this->_setFontRegular($page, 12);
		$page->drawText('Account Name: Archway International HK Limited', 85, $this->y-45, 'UTF-8');
		$this->_setFontRegular($page, 12);
			$page->drawText('Thank you and best regards!', 220, $this->y-85, 'UTF-8');
        return $page;
    }
	
	public function _drawHeaders(Zend_Pdf_Page $page)
    {
        /* Add table head */
        
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
            'text'  => Mage::helper('sales')->__('Qty'),
            'feed'  => 455,
            'align' => 'right'
        );

        $lines[0][] = array(
            'text'  => Mage::helper('sales')->__('Unit Price'),
            'feed'  => 505,
            'align' => 'right'
        );

        $lines[0][] = array(
            'text'  => Mage::helper('sales')->__('Total'),
            'feed'  => 565,
            'align' => 'right'
        );
		
        $lineBlock = array(
            'lines'  => $lines,
            'height' => 5
        );
		 $this->_setFontRegular($page, 12);
        $this->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->y -= 20;
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
	
	protected function insertLogos(&$page, $store = null)
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
		
        $image = Mage::getStoreConfig('sales/identity/logo_ws', $store);
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
		$order = Mage::getModel('sales/order')->loadByIncrementId($orderData['increment_id']);
		if ($order->hasInvoices()) {
			
			foreach ($order->getInvoiceCollection() as $inv) {
				$invIncrementIDs = $inv->getIncrementId();
			}
		}
		
		
		
        $this->y = $this->y ? $this->y : 815;
        $top = $this->y;
        $top -= 20;
		$page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->_setFontBold($page, 22);
		$page->drawText('Pro-Forma Invoice', 390, ($top - 15), 'UTF-8');
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
		$page->drawText($invIncrementIDs, 400, ($top - 40), 'UTF-8');
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
			$page->drawText(Mage::getModel('core/date')->date('d M Y', $orderData['created_at']), 36, $this->y, 'UTF-8');
		
			

			
			$page->drawText("Order Id#".$orderData['increment_id'], $x + 145, $this->y, 'UTF-8');
			if($orderData['client_po']){
				$page->drawText("Client Ref PO#".$orderData['client_po'], $x + 145, $this->y-15, 'UTF-8');
			}
			
			$page->drawText("Payment Upon Receipt", $x + 375, $this->y, 'UTF-8');
			$this->y -= 50;
			
		
			

            $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
           /*  $page->setLineWidth(0.5);
            $page->drawRectangle(25, $this->y, 275, $this->y-25);
            $page->drawRectangle(275, $this->y, 275, $this->y-25);
            $page->drawRectangle(275, $this->y, 275, $this->y-25);

            $this->y -= 15;
            $this->_setFontBold($page, 12);
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
            $page->drawText(Mage::helper('clientpo')->__('Date'), 35, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('clientpo')->__('Order No.'), 185, $this->y , 'UTF-8');
            $page->drawText(Mage::helper('clientpo')->__('Terms'), 285, $this->y , 'UTF-8');

            $this->y -=10;
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));

            $this->_setFontRegular($page, 10);
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));

            $paymentLeft = 35;
            $yPayments   = $this->y - 15;
       

        foreach ($payment as $value){
            if (trim($value) != '') {
                //Printing "Payment Method" lines
                $value = preg_replace('/<br[^>]*>/i', "\n", $value);
                foreach (Mage::helper('core/string')->str_split($value, 45, true, true) as $_value) {
                    $page->drawText(strip_tags(trim($_value)), $paymentLeft, $yPayments, 'UTF-8');
                    $yPayments -= 15;
                }
            }
        } */

        /* if ($order->getIsVirtual()) {
            // replacement of Shipments-Payments rectangle block
            $yPayments = min($addressesEndY, $yPayments);
            $page->drawLine(25,  ($top - 25), 25,  $yPayments);
            $page->drawLine(570, ($top - 25), 570, $yPayments);
            $page->drawLine(25,  $yPayments,  570, $yPayments);

            $this->y = $yPayments - 15;
        } else {
            $topMargin    = 15;
            $methodStartY = $this->y;
            $this->y     -= 15;

            foreach (Mage::helper('core/string')->str_split($shippingMethod, 45, true, true) as $_value) {
                $page->drawText(strip_tags(trim($_value)), 285, $this->y, 'UTF-8');
                $this->y -= 15;
            }

            $yShipments = $this->y;
            $totalShippingChargesText = "(" . Mage::helper('sales')->__('Total Shipping Charges') . " "
                . $order->formatPriceTxt($order->getShippingAmount()) . ")";

            $page->drawText($totalShippingChargesText, 285, $yShipments - $topMargin, 'UTF-8');
            $yShipments -= $topMargin + 10;

            $tracks = array();
            if ($shipment) {
                $tracks = $shipment->getAllTracks();
            }
            if (count($tracks)) {
                $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
                $page->setLineWidth(0.5);
                $page->drawRectangle(285, $yShipments, 510, $yShipments - 10);
                $page->drawLine(400, $yShipments, 400, $yShipments - 10);
                //$page->drawLine(510, $yShipments, 510, $yShipments - 10);

                $this->_setFontRegular($page, 9);
                $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
                //$page->drawText(Mage::helper('sales')->__('Carrier'), 290, $yShipments - 7 , 'UTF-8');
                $page->drawText(Mage::helper('sales')->__('Title'), 290, $yShipments - 7, 'UTF-8');
                $page->drawText(Mage::helper('sales')->__('Number'), 410, $yShipments - 7, 'UTF-8');

                $yShipments -= 20;
                $this->_setFontRegular($page, 8);
                foreach ($tracks as $track) {

                    $CarrierCode = $track->getCarrierCode();
                    if ($CarrierCode != 'custom') {
                        $carrier = Mage::getSingleton('shipping/config')->getCarrierInstance($CarrierCode);
                        $carrierTitle = $carrier->getConfigData('title');
                    } else {
                        $carrierTitle = Mage::helper('sales')->__('Custom Value');
                    }

                    //$truncatedCarrierTitle = substr($carrierTitle, 0, 35) . (strlen($carrierTitle) > 35 ? '...' : '');
                    $maxTitleLen = 45;
                    $endOfTitle = strlen($track->getTitle()) > $maxTitleLen ? '...' : '';
                    $truncatedTitle = substr($track->getTitle(), 0, $maxTitleLen) . $endOfTitle;
                    //$page->drawText($truncatedCarrierTitle, 285, $yShipments , 'UTF-8');
                    $page->drawText($truncatedTitle, 292, $yShipments , 'UTF-8');
                    $page->drawText($track->getNumber(), 410, $yShipments , 'UTF-8');
                    $yShipments -= $topMargin - 5;
                }
            } else {
                $yShipments -= $topMargin - 5;
            }

            $currentY = min($yPayments, $yShipments);

            // replacement of Shipments-Payments rectangle block
            $page->drawLine(25,  $methodStartY, 25,  $currentY); //left
            $page->drawLine(25,  $currentY,     570, $currentY); //bottom
            $page->drawLine(570, $currentY,     570, $methodStartY); //right

            $this->y = $currentY;
            $this->y -= 15;
        } */
    }

}
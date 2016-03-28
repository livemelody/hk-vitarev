<?php

class VR_SalesReport_Model_Report extends Mage_Core_Model_Abstract
{
    public function createReport(Zend_Date $from,Zend_Date $to)
    {
        try {
            $filter_cond = array("from" => $from->toString("yyyy-MM-dd"), "to"=>$to->toString("yyyy-MM-dd"));
            $collection = Mage::getResourceModel("sales/order_item_collection");
            $collection->addFieldToFilter("created_at", $filter_cond);
            $io = new Varien_Io_File();
            $path = Mage::getBaseDir('var') . DS . 'export' . DS . 'ordered_products';
            $name = $to->toString("yyyyMMdd")."-".$from->toString("yyyyMMdd");
            $file = $path . DS . $name . '.csv';
            $io->setAllowCreateFolders(true);
            $io->open(array('path' => $path));
            $io->streamOpen($file, 'w+');
            $io->streamLock(true);
            $io->streamWriteCsv($this->_getCsvHeaders());
            foreach ($collection as $item) {
                $io->streamWriteCsv($this->_createRow($item));
            }
            $io->streamClose();
            $io->close();
            $model = Mage::getModel("vr_salesreport/file")->loadByPeriod($name);
            $model->setFilename($file);
            $model->setPeriod($name);
            $model->save();
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    protected function _getCsvHeaders()
    {
        return array(
            "Period",
            "Order #",
            "Order Status",
            "Order Date",
            "Item #",
            "Parent Item #",
            "UPC",
            "SKU",
            "BUNDLE ITEM",
            "Bill to Name",
            "Customer Email",
            "Product Name",
            "Qty. Ordered",
            "Customer Group", "Country", "City",
            "Qty. Invoiced", "Qty. Shipped", "Qty. Refunded",
            "Price", "Original Price", "Sub Total", "Tax", "Discount", "Total", "Total incl. Tax", "Invoiced", "Tax Invoiced", "Invoiced incl. Tax",
            "Refunded", "Tax Refunded", "Refunded incl. Tax",
            "Payment Method"
        );
    }

    protected function _createRow($item)
    {
        $row = array();
        try {
            $date = new Zend_Date($item->getCreatedAt());
            $row['period'] = $date->toString("MM/dd/yyyy");
            $order = $item->getOrder();
            $row['order_id'] = $order->getIncrementId();
            $row['order_status'] = $order->getStatus();
            $row['order_date'] = $item->getCreatedAt();
            $row['item_id'] = $item->getId();
            $row['parent_id'] = $item->getParentItemId();
            $row['upc'] =  Mage::getModel("catalog/product")->getResource()->getAttributeRawValue($item->getProductId(),"upc",0);
            $row['sku'] = $item->getSku();
            $row['bundle_item'] = ($item->getParentItemId()) ? "Yes" : "";
            $row['bill_to'] = $order->getBillingAddress()->getName();
            $row['customer_email'] = $order->getBillingAddress()->getEmail();
            $row['product_name'] = $item->getName();
            $row['qty_ordered'] = $item->getQtyOrdered();
            $customer = Mage::getModel("customer/customer")->load($order->getCustomerId());
            $row['customer_group'] = Mage::getModel("customer/group")->load($customer->getGroupId())->getCode();
            $row['qty_ordered'] = $item->getQtyOrdered();
            $row['country'] = $order->getBillingAddress()->getCountry();
            $row['city'] = $order->getBillingAddress()->getCity();
            $row['qty_invoiced'] = $item->getQtyInvoiced();
            $row['qty_shipped'] = $item->getQtyShipped();
            $row['qty_refunded'] = $item->getQtyRefunded();
            $row['price'] = $item->getBasePrice();
            $row['original_price'] = $item->getBaseOriginalPrice();
            $row['subtotal'] = $item->getBaseOriginalPrice();
            $row['tax'] = $item->getBaseTaxAmount();
            $row['discount'] = $item->getBaseDiscountAmount();
            $row['total'] = $item->getBaseRowTotal();
            $row['total_incl_tax'] = $item->getBaseRowTotalInclTax();
            $row['invoiced'] =$item->getBaseRowInvoiced();
            $row['tax_invoiced'] = $item->getBaseTaxInvoiced();
            $row['invoiced_incl_tax'] = $item->getData('base_row_total_incl_tax');
            $row['refunded'] = $item->getBaseAmountRefunded();
            $row['tax_refunded'] = $item->getBaseTaxRefunded();
            $row['refunded_incl_tax'] = (float)$item->getBaseAmountRefunded() + (float)$item->getBaseTaxRefunded();
            $row['payment_method'] = $order->getPayment()->getMethod();
        } catch (Exception $e) {
            Mage::logException($e);
        }
        return $row;
    }
}
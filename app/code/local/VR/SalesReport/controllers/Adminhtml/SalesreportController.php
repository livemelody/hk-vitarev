<?php

class VR_SalesReport_Adminhtml_SalesreportController extends Mage_Adminhtml_Controller_Action
{

    public function indexAction()
    {
        $report = Mage::getModel("vr_salesreport/report");
        if ($this->getRequest()->getParam("is_create")) {
            $to = new Zend_Date($this->getRequest()->getParam('to'),'MM/dd/yyyy');
            $from = new Zend_Date($this->getRequest()->getParam('from'),'MM/dd/yyyy');
            if ($to < $from) {
                $this->_getSession()->addError($this->__('"From" date must be early than "to" date'));
            } else {
                $report->createReport($from,$to);
            }
        }
        $this->loadLayout()->renderLayout();
    }


    public function downloadAction()
    {
        $entityid = $this->getRequest()->getParam('file_id');
        $file = Mage::getModel('vr_salesreport/file')->load($entityid);
        if (!is_file($file->getFilename()) || !is_readable($file->getFilename())) {
            throw new Exception ();
        }
        $this->getResponse()
            ->setHttpResponseCode(200)
            ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
            ->setHeader('Pragma', 'public', true)
            ->setHeader('Content-type', 'application/force-download')
            ->setHeader('Content-Length', filesize($file->getFilename()))
            ->setHeader('Charset', 'utf-8')
            ->setHeader('Content-Disposition', 'attachment' . '; filename=' . basename($file->getFilename()));
        $this->getResponse()->clearBody();
        $this->getResponse()->sendHeaders();
        readfile($file->getFilename());
        exit;
    }

}
<?php
/**
* aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Sociable
 * @version    1.3
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */




class AW_Sociable_IndexController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
        $this->_redirect('/');
    }

    public function saveClickAction() {

        $data = $this->getRequest()->getParams();
        $id = (int) $data['service_id'];
        if ($id) {
            $service = Mage::getModel('sociable/service')->load($id);
            if ($service->getId() && $service->getStatus()) {
                $storeId = Mage::app()->getStore()->getId();

                if ($UID = Mage::helper('sociable')->getClickUID()) {
                    if (!Mage::helper('sociable')->checkUniqueClick($id, $UID, $storeId, $data['entity_type'], $data['entity_id'])) {
                        Mage::helper('sociable')->saveClick($id, $UID, $storeId, $data['entity_type'], $data['entity_id']);
                        return true;
                    }
                    else
                        return false;
                }
                else {
                    $UID = Mage::helper('sociable')->setClickUID();
                    Mage::helper('sociable')->saveClick($id, $UID, $storeId, $data['entity_type'], $data['entity_id']);
                    return true;
                }
            }
        }
        return false;
    }

    public function redirectAction() {

        $serviceId = (int) ($this->getRequest()->getParam('service'));
        if ($serviceId) {
            $service = Mage::getModel('sociable/service')->load($serviceId);

            if ($service->getId() && $service->getStatus()) {
                $storeId = Mage::app()->getStore()->getId();
                $entityType = $this->getRequest()->getParam('entity_type');
                $entityId = $this->getRequest()->getParam('entity_id');
                if ($UID = Mage::helper('sociable')->getClickUID()) {
                    if (!Mage::helper('sociable')->checkUniqueClick($serviceId, $UID, $storeId, $entityType, $entityId)) {
                        Mage::helper('sociable')->saveClick($serviceId, $UID, $storeId, $entityType, $entityId);
                    } else {
                        
                    }
                } else {
                    $UID = Mage::helper('sociable')->setClickUID();
                    Mage::helper('sociable')->saveClick($serviceId, $UID, $storeId, $entityType, $entityId);
                }
                //get link
                $decoder = Mage::helper('core/url');
                $pageUrl = $decoder->urlDecode($this->getRequest()->getParam('url'));

                if ($service->getShortUrl() && Mage::helper('sociable')->getEnabledBitly()) {
                    $pageUrl = Mage::getModel('sociable/bitly')->getBitlyLink($pageUrl);
                }

                $pageTitle = $decoder->urlDecode($this->getRequest()->getParam('title'));
                $pageTitle = str_replace('amp%3B', '', str_replace('+', '%20', urlencode($pageTitle)));
                
                if ($entityType === 'product') {
                    $_product = Mage::getModel('catalog/product')->load($entityId);
                    $pageMedia = $_product->getImageUrl();
                } else {
                    $pageMedia=Mage::getDesign()->getSkinUrl(Mage::getStoreConfig('design/header/logo_src'));                    
                }
                
                $headers = get_headers($pageMedia);
                if(!strpos($headers[0], '200')) {
                    $pageMedia = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'sociable/default.jpg';
                }
                
                //build sociable link
                $link = $service->getServiceUrl();
                $link = str_replace(array('{title}', '{url}', '{media}'), array($pageTitle, urlencode($pageUrl), urlencode($pageMedia)),$link);
                $this->_redirectUrl($link);
            } else {
                $this->_redirect('/');
            }
        } else {
            $this->_redirect('/');
        }
    }

}
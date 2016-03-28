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




if ((string)Mage::getConfig()->getNode('modules/AW_FBIntegrator/active') == 'true') {

    if (!class_exists('AW_Sociable_Block_Fbintegrator')) {

        class SocBlock extends AW_Fbintegrator_Block_Like {

        }
    }
        
} else {

    if (!class_exists('AW_Sociable_Block_Fbintegrator')) {

        class SocBlock extends Mage_Core_Block_Template {

        }
    }
    
}

class AW_Sociable_Block_Fbintegrator extends SocBlock {
    
    public function __construct() {
        if ((!Mage::getStoreConfigFlag('advanced/modules_disable_output/AW_FBIntegrator')) &&
            ((string)Mage::getConfig()->getNode('modules/AW_FBIntegrator/active') == 'true')) {
            $this->setTemplate('fbintegrator/fb_like.phtml');
            parent::__construct();
        }
    }
    
    protected function _toHtml() {
        if ((!Mage::getStoreConfigFlag('advanced/modules_disable_output/AW_FBIntegrator')) &&
            ((string)Mage::getConfig()->getNode('modules/AW_FBIntegrator/active') == 'true')) {
            $this->setTemplate('fbintegrator/fb_like.phtml');
            return parent::_toHtml();
        }
    }

}
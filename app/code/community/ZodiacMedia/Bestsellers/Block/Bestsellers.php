<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please contact us via our website at 
 * http://www.zodiacmedia.co.uk/contact-us so we can send you a copy immediately.
 *
 * @category   ZodiacMedia
 * @package    ZodiacMedia_Bestsellers
 * @copyright  Copyright (c) 2013 Zodiac Media Ltd (http://www.zodiacmedia.co.uk)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ZodiacMedia_Bestsellers_Block_Bestsellers extends Mage_Catalog_Block_Product_Abstract {
    
    /**
     * Internal contructor
     *
     */
    protected function _construct()
    {
        parent::_construct();

        $this->addColumnCountLayoutDepend('empty', 6)
            ->addColumnCountLayoutDepend('one_column', 5)
            ->addColumnCountLayoutDepend('two_columns_left', 4)
            ->addColumnCountLayoutDepend('two_columns_right', 4)
            ->addColumnCountLayoutDepend('three_columns', 3);
        
        $this->addData(array(
            'cache_lifetime'    => 86400,
            'cache_tags'        => array(Mage_Catalog_Model_Product::CACHE_TAG),
        ));
    }
    
    /**
     * Get data for caching of block content.
     *
     * @return array
     */
    public function getCacheKeyInfo() {
        return array(
           'CATALOG_PRODUCT_BESTSELLING',
           Mage::app()->getStore()->getId(),
           Mage::getDesign()->getPackageName(),
           Mage::getDesign()->getTheme('template'),
           Mage::getSingleton('customer/session')->getCustomerGroupId(),
           'template' => $this->getTemplate(),
           $this->getProductsCount()
        );
    }

    /**
     * Prepare collection with new products and applied page limits.
     *
     * return ZodiacMedia_Bestsellers_Block_Bestsellers
     */
    protected function _beforeToHtml() {
		
        $collection = Mage::getResourceModel('bestsellers/product_collection');
        
        $collection = $this->_addProductAttributesAndPrices($collection)
            ->addOrderedQty()
            ->setOrder('ordered_qty', 'desc')
            ->setPageSize($this->getProductsCount())
            ->setCurPage(1)
            ->load();
        
		$this->setProductCollection($collection);
		
        return parent::_beforeToHtml();
    }
}

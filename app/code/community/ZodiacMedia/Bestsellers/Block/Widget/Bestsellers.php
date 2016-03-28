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
class ZodiacMedia_Bestsellers_Block_Widget_Bestsellers
    extends ZodiacMedia_Bestsellers_Block_Bestsellers
    implements Mage_Widget_Block_Interface {
    
    const DEFAULT_PRODUCTS_COUNT    = 12;
    const DEFAULT_THUMBNAIL_SIZE    = 135;
    
    /**
     * Internal contructor
     *
     */
    protected function _construct() {
        parent::_construct();
        
        $this->addPriceBlockType('bundle', 'bundle/catalog_product_price', 'bundle/catalog/product/price.phtml');
    }

    /**
     * Retrieve how many products should be displayed.
     *
     * @return int
     */
    public function getProductsCount() {
        if (! $this->hasData('products_count'))
            return self::DEFAULT_PRODUCTS_COUNT;

        return $this->_getData('products_count');
    }
    
    /**
     * Retrieve thumbnail size.
     *
     * @return int
     */
    public function getThumbnailSize() {
        if (! $this->hasData('thumbnail_size'))
            return self::DEFAULT_THUMBNAIL_SIZE;
        
        return (int) $this->_getData('thumbnail_size');
    }
}

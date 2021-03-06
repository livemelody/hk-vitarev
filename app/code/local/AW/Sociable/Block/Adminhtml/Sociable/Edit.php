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


class AW_Sociable_Block_Adminhtml_Sociable_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'sociable';
        $this->_controller = 'adminhtml_sociable';
        
        $this->_updateButton('save', 'label', Mage::helper('sociable')->__('Save'));
        $this->_updateButton('delete', 'label', Mage::helper('sociable')->__('Delete'));
        
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('sociable_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'sociable_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'sociable_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('sociable_data') && Mage::registry('sociable_data')->getId() ) {
            return Mage::helper('sociable')->__("Edit Service '%s'", $this->htmlEscape(Mage::registry('sociable_data')->getTitle()));
        } else {
            return Mage::helper('sociable')->__('Add Service');
        }
    }
}
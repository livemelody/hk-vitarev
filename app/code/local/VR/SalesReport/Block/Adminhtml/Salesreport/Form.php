<?php
class VR_SalesReport_Block_Adminhtml_Salesreport_Form extends Mage_Adminhtml_Block_Widget_Form
{

    public function __construct(){
        parent::__construct();
        //$this->_addButton('add', array(
        //    'label'     => $this->__('Create'),
        //    'class'     => 'add',
        //    'type'      => 'submit'
        //));
    }
    /**
     * Add fieldset with general report fields
     *
     * @return Mage_Adminhtml_Block_Report_Filter_Form
     */
    protected function _prepareForm()
    {
        $actionUrl = $this->getUrl('adminhtml/salesreport/index');
        $form = new Varien_Data_Form(
            array('id' => 'create_report_form', 'action'=>$actionUrl, 'method' => 'post')
        );
        $htmlIdPrefix = 'sales_report_';
        $form->setHtmlIdPrefix($htmlIdPrefix);
        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>$this->__('Choose period for report')));

        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);

        $fieldset->addField('is_create', 'hidden', array(
            'name'      => 'is_create',
            'value' => '1'
        ));


        $fieldset->addField('from', 'date', array(
            'name'      => 'from',
            'format'    => $dateFormatIso,
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'label'     => Mage::helper('reports')->__('From'),
            'title'     => Mage::helper('reports')->__('From'),
            'required'  => true
        ));

        $fieldset->addField('to', 'date', array(
            'name'      => 'to',
            'format'    => $dateFormatIso,
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'label'     => Mage::helper('reports')->__('To'),
            'title'     => Mage::helper('reports')->__('To'),
            'required'  => true
        ));

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

}
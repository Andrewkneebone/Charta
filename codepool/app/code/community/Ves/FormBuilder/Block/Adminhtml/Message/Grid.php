<?php
/**
 * Venustheme
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Venustheme EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.venustheme.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.venustheme.com/ for more information
 *
 * @category   Ves
 * @package    Ves_FormBuilder
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */

/**
 * Form Builder extension
 *
 * @category   Ves
 * @package    Ves_FormBuilder
 * @author     Venustheme Dev Team <venustheme@gmail.com>
 */
class Ves_FormBuilder_Block_Adminhtml_Message_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId("messageGrid");
        $this->setDefaultSort("message_id");
        $this->setDefaultDir("DESC");
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel("ves_formbuilder/message")->getCollection();

        $form_id = $this->getRequest()->getParam("form_id");
        if($form_id) {
            $collection->addFormBuilderFilter($form_id);
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn("message_id", array(
            "header" => Mage::helper("ves_formbuilder")->__("ID"),
            "align" => "right",
            "width" => "50px",
            "type" => "number",
            "index" => "message_id",
            ));

        $this->addColumn('message_status', array(
            'header'    => Mage::helper('ves_formbuilder')->__('Status'),
            'align'     =>'left',
            'index'     => 'created',
            'renderer'  => 'Ves_FormBuilder_Block_Adminhtml_Renderer_Status',
            'filter'    => false,
            'sortable'  => false
            ));
        if($form_id = $this->getRequest()->getParam("form_id")) {
            $this->addColumn("form_id", array(
            "header" => Mage::helper("ves_formbuilder")->__("Custom Form Name"),
            "align" => "right",
            "index" => "form_id",
            "type"  => "options",
            'renderer' => 'Ves_FormBuilder_Block_Adminhtml_Renderer_Formlink',
            'filter'    => false,
            'sortable'  => false
            ));
        } else {
            $this->addColumn("form_id", array(
            "header" => Mage::helper("ves_formbuilder")->__("Custom Form Name"),
            "align" => "right",
            "index" => "form_id",
            "type"  => "options",
            'renderer' => 'Ves_FormBuilder_Block_Adminhtml_Renderer_Formlink',
            //"type"  => "options",
            'options' => Ves_FormBuilder_Block_Adminhtml_Message_Grid::getListCustomForms(),
            'filter_condition_callback' => array (
                $this,'_filterFormBuilderCondition' )
            ));
        }
        

        $this->addColumn("message", array(
            "header" => Mage::helper("ves_formbuilder")->__("Message"),
            "align" => "left",
            "index" => "message",
            "type" => "text",
            'renderer'  => 'Ves_FormBuilder_Block_Adminhtml_Renderer_Message'
            ));

        $this->addColumn("customer_id", array(
            "header" => Mage::helper("ves_formbuilder")->__("Customer Email"),
            "align" => "right",
            "index" => "customer_id",
            "type" => "text",
            'renderer'  => 'Ves_FormBuilder_Block_Adminhtml_Renderer_CustomerEmail',
            'filter_condition_callback' => array (
                $this,'_filterCustomerEmailCondition' )
            ));

        $this->addColumn("product_id", array(
            "header" => Mage::helper("ves_formbuilder")->__("Product"),
            "align" => "right",
            "index" => "product_id",
            'renderer'  => 'Ves_FormBuilder_Block_Adminhtml_Renderer_Product',
            'filter' => false
            ));

        $this->addColumn('created', array(
            'header'    => Mage::helper('ves_formbuilder')->__('Creation Time'),
            'align'     =>'left',
            'index'     => 'created',
            ));

        $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('sales')->__('XML'));

        return parent::_prepareColumns();
    }
    /**
     * Helper function to add store filter condition
     *
     * @param Mage_Core_Model_Mysql4_Collection_Abstract $collection Data collection
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column Column information to be filtered
     */
    protected function _filterCustomerEmailCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        $this->getCollection()->addCustomerEmailFilter($value);
    }

    /**
     * Helper function to add store filter condition
     *
     * @param Mage_Core_Model_Mysql4_Collection_Abstract $collection Data collection
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column Column information to be filtered
     */
    protected function _filterFormBuilderCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        $this->getCollection()->addFormBuilderFilter($value);
    }
    /**
     * Helper function to add store filter condition
     *
     * @param Mage_Core_Model_Mysql4_Collection_Abstract $collection Data collection
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column Column information to be filtered
     */
    protected function _filterProductNameCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        $this->getCollection()->addProductNameFilter($value);
        $this->getCollection()->getSelect()->group("main_table.message_id");
    }
    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }
    public function getRowUrl($row)
    {
        return $this->getUrl("*/*/edit", array("id" => $row->getId()));
    }


    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('ids');
        $this->getMassactionBlock()->setUseSelectAll(true);

        $this->getMassactionBlock()->addItem('remove_block', array(
            'label' => Mage::helper('ves_formbuilder')->__('Delete Messages'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('ves_formbuilder')->__('Are you sure?')
            ));
        return $this;
    }

    static public function getOptionYesNo()
    {
        $data_array = array();
        $data_array[1] = Mage::helper('ves_formbuilder')->__('Enabled');
        $data_array[2] = Mage::helper('ves_formbuilder')->__('Disabled');
        return ($data_array);
    }

    static public function getListCustomForms() {
        $data_array = array();
        $collection = Mage::getModel("ves_formbuilder/form")->getCollection();
        if(0 < $collection->getSize()) {
            foreach($collection as $item) {
                $data_array[$item->getId()] = $item->getTitle();
            }
        }
        return ($data_array);
    }

    static public function getValueYesNo()
    {
        $data_array = array();
        foreach (Ves_FormBuilder_Block_Adminhtml_Message_Grid::getOptionYesNo() as $k => $v) {
            $data_array[] = array('value' => $k, 'label' => $v);
        }
        return ($data_array);

    }

}
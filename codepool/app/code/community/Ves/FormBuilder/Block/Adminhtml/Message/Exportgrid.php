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
class Ves_FormBuilder_Block_Adminhtml_Message_Exportgrid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected $_form_profile_id;
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
        $controller_name = $this->getRequest()->getControllerName();
        $collection = Mage::getModel("ves_formbuilder/message")->getCollection();
        $collection->prepareExportData();
        $form_id = $this->getFormProfileId();
        if($form_id) {
            $collection->setFormProfileId($form_id);
            $collection->addFormBuilderFilter($form_id);
        }

        $internal_ids = $this->getRequest()->getParam("internal_ids");
        if($internal_ids) {
            $ids = explode(",",$internal_ids);
            $collection->addFieldToFilter('message_id', array('in'=>$ids));
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $form_id = $this->getFormProfileId();

        if(Mage::getStoreConfig('ves_formbuilder/export_settings/enable_message_id')) {
            $this->addColumn("message_id", array(
                "header" => Mage::helper("ves_formbuilder")->__("message_id"),
                "index" => "message_id",
                ));
        }
        if(Mage::getStoreConfig('ves_formbuilder/export_settings/enable_form_id')) {
            $this->addColumn("form_id", array(
                "header" => Mage::helper("ves_formbuilder")->__("form_id"),
                "index" => "form_id",
                'filter_condition_callback' => array (
                        $this,'_filterFormBuilderCondition' )
                ));
        }

        if(Mage::getStoreConfig('ves_formbuilder/export_settings/enable_form_title') && $form_id) {
            $this->addColumn("title", array(
                "header" => Mage::helper("ves_formbuilder")->__("Form Title"),
                "index" => "title",
                ));
        }

        if(Mage::getStoreConfig('ves_formbuilder/export_settings/export_fields_columns') && $form_id) {
            $form_model = Mage::getModel("ves_formbuilder/form")->load($form_id);
            $design = $form_model->getDesign();
            $design = Zend_Json::decode($design);
            $fields = isset($design['fields'])?$design['fields']:array();
            if($fields) {
                foreach($fields as $field) {
                    if($field &&  isset($field['label']) && $field['label']) {
                       $this->addColumn($field['label'], array(
                        "header" => $field['label'],
                        "index" => $field['label'],
                        ));
                    }
                }
            }
        }

        if(Mage::getStoreConfig('ves_formbuilder/export_settings/enable_product')) {
            $this->addColumn("product_id", array(
                "header" => Mage::helper("ves_formbuilder")->__("product_id"),
                "index" => "product_id",
                ));
        }

        if(Mage::getStoreConfig('ves_formbuilder/export_settings/enable_customer')) {
            $this->addColumn("customer_id", array(
                "header" => Mage::helper("ves_formbuilder")->__("customer_id"),
                "index" => "customer_id",
                ));

            $this->addColumn("customer_email", array(
                "header" => Mage::helper("ves_formbuilder")->__("customer_email"),
                "index" => "customer_id",
                "trim_html" => true,
                'renderer'  => 'Ves_FormBuilder_Block_Adminhtml_Renderer_CustomerEmail'
                ));
        }

        if(Mage::getStoreConfig('ves_formbuilder/export_settings/enable_subject')) {
            $this->addColumn("subject", array(
                "header" => Mage::helper("ves_formbuilder")->__("subject"),
                "index" => "subject",
                ));
        }

        if(Mage::getStoreConfig('ves_formbuilder/export_settings/enable_email_from')) {
            $this->addColumn("email_form", array(
                "header" => Mage::helper("ves_formbuilder")->__("email_form"),
                "index" => "email_form",
                ));
        }
        if(Mage::getStoreConfig('ves_formbuilder/export_settings/enable_created')) {
            $this->addColumn("created", array(
                "header" => Mage::helper("ves_formbuilder")->__("created"),
                "index" => "created",
                ));
        }

        if(Mage::getStoreConfig('ves_formbuilder/export_settings/enable_message')) {
            $export_message_plaintext = Mage::getStoreConfig('ves_formbuilder/export_settings/export_plaintext');

            if($export_message_plaintext) {

                $this->addColumn("message", array(
                    "header" => Mage::helper("ves_formbuilder")->__("message"),
                    "index" => "message",
                    'renderer'  => 'Ves_FormBuilder_Block_Adminhtml_Renderer_Messagetext'
                ));

            } else {
                $this->addColumn("message", array(
                    "header" => Mage::helper("ves_formbuilder")->__("message"),
                    "index" => "message",
                    ));
            }
        }
        
        if(Mage::getStoreConfig('ves_formbuilder/export_settings/enable_ip')) {
            $this->addColumn("ip_address", array(
                "header" => Mage::helper("ves_formbuilder")->__("ip_address"),
                "index" => "ip_address",
                ));
        }

        if(Mage::getStoreConfig('ves_formbuilder/export_settings/enable_params')) {
            $this->addColumn("params", array(
                "header" => Mage::helper("ves_formbuilder")->__("params"),
                "index" => "params",
                ));
        }
        return parent::_prepareColumns();
    }

    public function getFormProfileId() {
        if(!$this->_form_profile_id) {
            $this->_form_profile_id = $this->getRequest()->getParam("form_id");
            $this->_form_profile_id = (int)$this->_form_profile_id;
        }
        return $this->_form_profile_id;
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
        $this->getCollection()->setFormProfileId($value);
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
        $this->_form_profile_id = $value;

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
            'label' => Mage::helper('ves_formbuilder')->__('Remove Block'),
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
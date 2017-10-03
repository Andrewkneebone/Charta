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
class Ves_FormBuilder_Block_Adminhtml_Formgroupfield_Exportgrid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId("formgroupfieldGrid");
        $this->setDefaultSort("group_id");
        $this->setDefaultDir("DESC");
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel("ves_formbuilder/groupfield")->getCollection();

        $IDList = $this->getRequest()->getParam('internal_ids');
        if($IDList) {
           $IDList = explode(",",$IDList);
           $collection->addFieldToFilter("form_id", array('in'=>$IDList));
        }

        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        foreach ($collection as $_form) {
            $results = $query = '';
            $query = 'SELECT store_id FROM ' . $resource->getTableName('ves_formbuilder/form_store').' WHERE form_id = '.$_form->getFormId();
            $results = $readConnection->fetchCol($query);
            $_form->setData('stores', implode('-', $results));
        }
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
         $this->addColumn("form_id", array(
            "header" => Mage::helper("ves_formbuilder")->__("form_id"),
            "align" => "right",
            "width" => "50px",
            "type" => "number",
            "index" => "form_id",
        ));

        $this->addColumn("title", array(
            "header" => Mage::helper("ves_formbuilder")->__("title"),
            "align" => "right",
            "index" => "title",
        ));

        $this->addColumn("identifier", array(
            "header" => Mage::helper("ves_formbuilder")->__("identifier"),
            "align" => "right",
            "index" => "identifier"
        ));

        $this->addColumn("email_receive", array(
            "header" => Mage::helper("ves_formbuilder")->__("email_receive"),
            "align" => "right",
            "width" => "30%",
            "index" => "email_receive",
        ));

        $this->addColumn("email_template", array(
            "header" => Mage::helper("ves_formbuilder")->__("email_template"),
            "align" => "right",
            "width" => "30%",
            "index" => "email_template",
        ));

        $this->addColumn("customer_group", array(
            "header" => Mage::helper("ves_formbuilder")->__("customer_group"),
            "align" => "right",
            "width" => "30%",
            "index" => "customer_group",
        ));

        $this->addColumn("show_captcha", array(
            "header" => Mage::helper("ves_formbuilder")->__("show_captcha"),
            "align" => "right",
            "width" => "30%",
            "index" => "show_captcha",
        ));

        $this->addColumn("show_toplink", array(
            "header" => Mage::helper("ves_formbuilder")->__("show_toplink"),
            "align" => "right",
            "width" => "30%",
            "index" => "show_toplink",
        ));

        $this->addColumn("submit_button_text", array(
            "header" => Mage::helper("ves_formbuilder")->__("submit_button_text"),
            "align" => "right",
            "width" => "30%",
            "index" => "submit_button_text",
        ));

        $this->addColumn("success_message", array(
            "header" => Mage::helper("ves_formbuilder")->__("success_message"),
            "align" => "right",
            "width" => "30%",
            "index" => "success_message",
        ));

        $this->addColumn("before_form_content", array(
            "header" => Mage::helper("ves_formbuilder")->__("before_form_content"),
            "align" => "right",
            "width" => "30%",
            "index" => "before_form_content",
        ));

        $this->addColumn("after_form_content", array(
            "header" => Mage::helper("ves_formbuilder")->__("after_form_content"),
            "align" => "right",
            "width" => "30%",
            "index" => "after_form_content",
        ));

        $this->addColumn("status", array(
            "header" => Mage::helper("ves_formbuilder")->__("status"),
            "align" => "right",
            "width" => "30%",
            "index" => "status",
        ));

        $this->addColumn("design", array(
            "header" => Mage::helper("ves_formbuilder")->__("design"),
            "align" => "right",
            "width" => "30%",
            "index" => "design",
        ));

        $this->addColumn("created", array(
            "header" => Mage::helper("ves_formbuilder")->__("created"),
            "align" => "right",
            "width" => "30%",
            "index" => "created",
        ));

        $this->addColumn("modified", array(
            "header" => Mage::helper("ves_formbuilder")->__("modified"),
            "align" => "right",
            "width" => "30%",
            "index" => "modified",
        ));

        $this->addColumn("settings", array(
            "header" => Mage::helper("ves_formbuilder")->__("settings"),
            "align" => "right",
            "width" => "30%",
            "index" => "settings",
        ));

        $this->addColumn("stores", array(
            "header" => Mage::helper("ves_formbuilder")->__("stores"),
            "index" => "stores",
        ));
        
        $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel'));

        return parent::_prepareColumns();
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

        $statuses = array(
                1 => Mage::helper('ves_formbuilder')->__('Enabled'),
                2 => Mage::helper('ves_formbuilder')->__('Disabled')
                );
        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
                'label'=> Mage::helper('ves_formbuilder')->__('Change status'),
                'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
                'additional' => array(
                'visibility' => array(
                        'name' => 'status',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => Mage::helper('ves_formbuilder')->__('Status'),
                        'values' => $statuses
                        )
                )
        ));
        $this->getMassactionBlock()->addItem('remove_block', array(
            'label' => Mage::helper('ves_formbuilder')->__('Remove Block'),
            'url' => $this->getUrl('*/adminhtml_formbuilder/massDelete'),
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

    static public function getValueYesNo()
    {
        $data_array = array();
        foreach (Ves_FormBuilder_Block_Adminhtml_Formbuilder_Grid::getOptionYesNo() as $k => $v) {
            $data_array[] = array('value' => $k, 'label' => $v);
        }
        return ($data_array);

    }


}
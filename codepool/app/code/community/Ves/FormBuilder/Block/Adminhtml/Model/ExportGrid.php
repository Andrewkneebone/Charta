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
class Ves_FormBuilder_Block_Adminhtml_Model_ExportGrid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId("modelGrid");
        $this->setDefaultSort("model_id");
        $this->setDefaultDir("ASC");
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel("ves_formbuilder/model")->getCollection();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn("model_id", array(
            "header" => Mage::helper("ves_formbuilder")->__("model_id"),
            "index" => "model_id",
            ));

        
        $this->addColumn("title", array(
            "header" => Mage::helper("ves_formbuilder")->__("title"),
            "index" => "title",
            ));

        $this->addColumn("category_id", array(
            "header" => Mage::helper("ves_formbuilder")->__("category_id"),
            "index" => "category_id",
            ));

        $this->addColumn("parent_id", array(
            "header" => Mage::helper("ves_formbuilder")->__("parent_id"),
            "index" => "parent_id",
            ));

        $this->addColumn("created", array(
            "header" => Mage::helper("ves_formbuilder")->__("created"),
            "index" => "created",
            ));

        $this->addColumn("modified", array(
            "header" => Mage::helper("ves_formbuilder")->__("modified"),
            "index" => "modified",
            ));

        $this->addColumn("status", array(
            "header" => Mage::helper("ves_formbuilder")->__("status"),
            "index" => "status",
            ));

        $this->addColumn("position", array(
            "header" => Mage::helper("ves_formbuilder")->__("position"),
            "index" => "position",
            ));

        return parent::_prepareColumns();
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
        $this->getCollection()->getSelect()->group("main_table.model_id");
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


    static public function getValueYesNo()
    {
        $data_array = array();
        foreach (Ves_FormBuilder_Block_Adminhtml_Message_Grid::getOptionYesNo() as $k => $v) {
            $data_array[] = array('value' => $k, 'label' => $v);
        }
        return ($data_array);

    }

}
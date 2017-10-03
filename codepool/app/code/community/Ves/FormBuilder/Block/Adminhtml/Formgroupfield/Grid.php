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
class Ves_FormBuilder_Block_Adminhtml_Formgroupfield_Grid extends Mage_Adminhtml_Block_Widget_Grid
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
        $controller_name = $this->getRequest()->getControllerName();
        $collection = Mage::getModel("ves_formbuilder/groupfield")->getCollection();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn("group_id", array(
            "header" => Mage::helper("ves_formbuilder")->__("ID"),
            "align" => "left",
            "width" => "50px",
            "type" => "number",
            "index" => "group_id",
            ));

        $this->addColumn("title", array(
            "header" => Mage::helper("ves_formbuilder")->__("Group Name"),
            "align" => "left",
            "index" => "title",

            ));

        $this->addColumn("identifier", array(
            "header" => Mage::helper("ves_formbuilder")->__("URL Key"),
            "align" => "left",
            "index" => "identifier",

            ));

        $this->addColumn('status', array(
            'header' => Mage::helper('ves_formbuilder')->__('Status'),
            'index' => 'status',
            'type' => 'options',
            'options' => Ves_FormBuilder_Block_Adminhtml_Formgroupfield_Grid::getOptionYesNo(),
            ));

        $this->addColumn('created', array(
            'header'    => Mage::helper('ves_formbuilder')->__('Creation Time'),
            'align'     =>'left',
            'index'     => 'created',
            ));

        $this->addColumn('modified', array(
            'header'    => Mage::helper('ves_formbuilder')->__('Modified Time'),
            'align'     =>'left',
            'index'     => 'modified',
            ));

        $this->addColumn('page_actions', array(
                'header'    => Mage::helper('cms')->__('Action'),
                'sortable'  => false,
                'filter'    => false,
                'renderer'  => 'Ves_FormBuilder_Block_Adminhtml_Renderer_Action',
            ));


    $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
    $this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel'));

    return parent::_prepareColumns();
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
        'label' => Mage::helper('ves_formbuilder')->__('Remove Group Field'),
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
    foreach (Ves_FormBuilder_Block_Adminhtml_Formgroupfield_Grid::getOptionYesNo() as $k => $v) {
        $data_array[] = array('value' => $k, 'label' => $v);
    }
    return ($data_array);

}


}
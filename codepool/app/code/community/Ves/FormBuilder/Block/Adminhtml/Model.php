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

class Ves_FormBuilder_Block_Adminhtml_Model extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {

        $this->_controller = "adminhtml_model";
        $this->_blockGroup = "ves_formbuilder";
        $this->_headerText = Mage::helper("ves_formbuilder")->__("Form Models Manager");
        $this->_addButtonLabel = Mage::helper("ves_formbuilder")->__("Create New Record");
        parent::__construct();

    }

    protected function _prepareLayout() {

        $this->setChild('import_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label'     => Mage::helper('ves_formbuilder')->__('Import CSV'),
                'onclick'   => "setLocation('".$this->getUrl('*/*/uploadCsv')."')",
                'class'   => 'add'
                ))
            );

        return parent::_prepareLayout();
    }

    public function getImportButtonHtml() {
        return $this->getChildHtml('import_button');
    }

}
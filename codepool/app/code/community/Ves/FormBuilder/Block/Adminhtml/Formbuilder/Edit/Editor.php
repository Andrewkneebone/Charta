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
class Ves_FormBuilder_Block_Adminhtml_Formbuilder_Edit_Editor extends Mage_Core_Block_Template
{
    var $_model = null;
    /**
     * Contructor
     */
    public function __construct($attributes = array())
    {
        $value = "";
        if (isset($attributes['value'])) {
            $value = $attributes['value'];
        }
        if(isset($attributes['model'])) {
            $this->_model = $attributes['model'];
        } else {
            $this->_model = Mage::registry("block_data");
        }

        $placeholder = Mage::getDesign()->getSkinUrl('images/catalog/product/placeholder/image.jpg',array('_area'=>'frontend'));

        $design = $this->_model->getDesign();

        $this->assign("placeholder", $placeholder);
        $this->assign("value", $value);
        $this->assign("builder_data", $this->_model);
        $this->assign("design", $design);

        $this->setTemplate("ves_formbuilder/edit/editor.phtml");

        parent::__construct();
    }
    
    protected function _getSkippedWidgets() {
        return null;
    }
    /**
     * Rendering block content
     *
     * @return string
     */
    function _toHtml() 
    {   
        return parent::_toHtml();
    }
    protected function getBlock()
    {
        return $this->_model;
    }

    public function getModelCategories() {
        $json = array();
        $collection = Mage::getModel("ves_formbuilder/category")->getCollection();
        if(0 < $collection->getSize()){
            foreach($collection as $item) {
                $tmp = array();
                $tmp['value'] = $item->getId();
                $tmp['label'] = $item->getTitle();
                $json[] = $tmp;
            }
        }
        return Mage::helper('core')->jsonEncode($json);
    }

}

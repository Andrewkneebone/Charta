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
class Ves_FormBuilder_Block_Adminhtml_Model_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }


        $form = new Varien_Data_Form();
        $model = Mage::registry("form_data");
        $this->setForm($form);
        $fieldset = $form->addFieldset("form_data", array("legend" => Mage::helper("ves_formbuilder")->__("Data Model information")));

        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);

        if($model->getId()){
            $fieldset->addField("model_id", "hidden", array(
                "label" => Mage::helper("ves_formbuilder")->__("Id"),
                "name" => "model_id",
                ));
        }

        $fieldset->addField("title", "text", array(
            "label" => Mage::helper("ves_formbuilder")->__("Title"),
            "name" => "title",
            "class" => "form-control required-entry",
            "required" => true
            ));

        $fieldset->addField("parent_id", "select", array(
            "label" => Mage::helper("ves_formbuilder")->__("Parent Id"),
            "name" => "parent_id",
            "class" => "form-control",
            "required" => false,
            'values' => Ves_FormBuilder_Block_Adminhtml_Model_Edit_Tab_Form::getParents()
            ));

        $fieldset->addField("category_id", "select", array(
            "label" => Mage::helper("ves_formbuilder")->__("Category Id"),
            "name" => "category_id",
            "class" => "form-control required-entry",
            "required" => true,
            'values' => Ves_FormBuilder_Block_Adminhtml_Model_Edit_Tab_Form::getCategories()
            ));

        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('ves_formbuilder')->__('Status'),
            'options'   => array(
                '1' => Mage::helper('cms')->__('Enabled'),
                '2' => Mage::helper('cms')->__('Disabled'),
                ),
            'name' => 'status',
            "class" => "form-control required-entry",
            "required" => true,
            ));

        $fieldset->addField("position", "text", array(
            "label" => Mage::helper("ves_formbuilder")->__("Position"),
            "name" => "position",
            "class" => "form-control",
            "required" => false
            ));

        if (Mage::getSingleton("adminhtml/session")->getBlockData()) {
            $form->setValues(Mage::getSingleton("adminhtml/session")->getBlockData());
            Mage::getSingleton("adminhtml/session")->getBlockData(null);
        } elseif ($model) {
            $form->setValues($model->getData());
        }

        return parent::_prepareForm();
    }
    
    static public function getParents()
    {
        $data_array = array();
        $data_array[] = array('value' => "", 'label' => Mage::helper("ves_formbuilder")->__("-- Select A Parent Item --"));
        $model = Mage::registry("form_data");
        $current_id = $model->getId();
        $collection = Mage::getModel('ves_formbuilder/model')->getCollection();

        foreach ($collection as $item) {
            if($current_id != $item->getId()) {
                $category_id = $item->getCategoryId();
                $category_title = Mage::getModel('ves_formbuilder/category')->load($category_id)->getTitle();
                $category_label = Mage::helper("ves_formbuilder")->__(" (Cate: %s)");
                $category_label = str_replace("%s", $category_title, $category_label);
                $data_array[] = array('value' => $item->getId(), 'label' => $item->getTitle()." ".$category_label);
            }
            
        }
        return $data_array;
    }

    static public function getCategories()
    {
        $data_array = array();
        $data_array[] = array('value' => "", 'label' => Mage::helper("ves_formbuilder")->__("-- Select A Category --"));

        $collection = Mage::getModel('ves_formbuilder/category')->getCollection();

        foreach ($collection as $item) {
            $data_array[] = array('value' => $item->getId(), 'label' => $item->getTitle());
            
        }
        return $data_array;
    }
    /**
     * Check permission for passed action
     *
     * @param string $action
     * @return bool
     */
    protected function _isAllowedAction($action)
    {
        return true;
    }
}
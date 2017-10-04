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
class Ves_FormBuilder_Block_Adminhtml_Formbuilder_Edit_Tab_Custom extends Mage_Adminhtml_Block_Widget_Form
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
        $fieldset = $form->addFieldset("form_data", array("legend" => Mage::helper("ves_formbuilder")->__("Extra Settings")));

        $fieldset->addField('custom_css', 'textarea', array(
            'name'      => 'custom_css',
            'label'     => Mage::helper('ves_formbuilder')->__('Custom CSS'),
//            'note' => Mage::helper('ves_blockbuilder')->__('Enter custom CSS code here. Your custom CSS will be outputted only on this particular form.'),
            'note' => Mage::helper('ves_formbuilder')->__('Enter custom CSS code here. Your custom CSS will be outputted only on this particular form.'),
            'style'     => 'width:90%;height:24em;'
        ));

        $fieldset->addField('custom_js', 'textarea', array(
            'name'      => 'custom_js',
            'label'     => Mage::helper('ves_formbuilder')->__('Custom JS'),
//            'note' => Mage::helper('ves_blockbuilder')->__('Enter custom JS code here. Your custom JS will be outputted only on this particular form.'),
            'note' => Mage::helper('ves_formbuilder')->__('Enter custom JS code here. Your custom JS will be outputted only on this particular form.'),
            'style'     => 'width:90%;height:24em;'
        ));

        if (Mage::getSingleton("adminhtml/session")->getBlockData()) {
            $form->setValues(Mage::getSingleton("adminhtml/session")->getBlockData());
            Mage::getSingleton("adminhtml/session")->getBlockData(null);
        } elseif ($model) {
            $form->setValues($model->getData());
        }

        return parent::_prepareForm();
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
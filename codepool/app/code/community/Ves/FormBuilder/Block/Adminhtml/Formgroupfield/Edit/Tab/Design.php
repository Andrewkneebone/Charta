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
class Ves_FormBuilder_Block_Adminhtml_Formgroupfield_Edit_Tab_Design extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {

        $form = new Varien_Data_Form();
        $model = Mage::registry("form_data");
        $this->setForm($form);
        $fieldset = $form->addFieldset("form_data", array("legend" => Mage::helper("ves_formbuilder")->__("Desgin Group Fields")));

        $lastEvent = "";
        //Here is what is interesting us
        //We add a new type, our type, to the fieldset
        //We call it extended_label
        $fieldset->addType('extended_editor','Ves_FormBuilder_Lib_Varien_Data_Form_Element_ExtendedEditor');

        $fieldset->addField('form_editor', 'extended_editor', array(
            'label'         => 'Form Editor',
            'name'          => 'form_editor',
            'block_id'      => 'wpo-widgetform',
            'model_data'    => $model,
            'required'      => false,
            'value'         => $this->getLastEventLabel($lastEvent),
            'label_style'   =>  'font-weight: bold;color:red;',
            ));


        if (Mage::getSingleton("adminhtml/session")->getBlockData()) {
            $form->setValues(Mage::getSingleton("adminhtml/session")->getBlockData());
            Mage::getSingleton("adminhtml/session")->getBlockData(null);
        } elseif ($model) {
            $form->setValues($model->getData());
        }

        return parent::_prepareForm();
    }
}
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
class Ves_FormBuilder_Block_Adminhtml_Formbuilder_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
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

        $config = Mage::getSingleton('cms/wysiwyg_config')->getConfig(
            array(
                'add_widgets' => false,
                'add_variables' => false,
                'add_images' => true,
                'encode_directives'             => false,
                'directives_url'                => Mage::getSingleton('adminhtml/url')->getUrl('*/cms_wysiwyg/directive'),
                'files_browser_window_url' => Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg_images/index'),
                'files_browser_window_width' => (int) Mage::getConfig()->getNode('adminhtml/cms/browser/window_width'),
                'files_browser_window_height'=> (int) Mage::getConfig()->getNode('adminhtml/cms/browser/window_height')
                )
            );

        $form = new Varien_Data_Form();
        $model = Mage::registry("form_data");
        $this->setForm($form);
        $fieldset = $form->addFieldset("form_data", array("legend" => Mage::helper("ves_formbuilder")->__("Form information")));

        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);

        if($model->getId()){
            $fieldset->addField("form_id", "hidden", array(
                "label" => Mage::helper("ves_formbuilder")->__("Id"),
                "name" => "form_id",
                ));
        }

        $fieldset->addField("title", "text", array(
            "label" => Mage::helper("ves_formbuilder")->__("Name"),
            "name" => "title",
            "class" => "form-control required-entry",
            "required" => true
            ));

        $fieldset->addField("identifier", "text", array(
            "label" => Mage::helper("ves_formbuilder")->__("URL Key"),
            "name" => "identifier",
            "class" => "form-control required-entry",
            "required" => true
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

        $fieldset->addField("page_title", "text", array(
            "label" => Mage::helper("ves_formbuilder")->__("Page Title"),
            "name" => "page_title"
            ));

        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('store_id', 'multiselect', array(
                'name' => 'stores[]',
                'label' => Mage::helper('ves_formbuilder')->__('Store View'),
                'title' => Mage::helper('ves_formbuilder')->__('Store View'),
                'required' => true,
                'values' => Mage::getSingleton('adminhtml/system_store')
                ->getStoreValuesForForm(false, true),
                ));
        }
        else {
            $fieldset->addField('store_id', 'hidden', array(
                'name' => 'stores[]',
                'value' => Mage::app()->getStore(true)->getId()
                ));
            $model->setStoreId(Mage::app()->getStore(true)->getId());
        }

        $fieldset->addField('customer_group', 'multiselect', array(
            'name' => 'customer_group[]',
            'label' => Mage::helper('ves_formbuilder')->__('Enable Custom Form for certain customer groups'),
            'title' => Mage::helper('ves_formbuilder')->__('Enable Custom Form for certain customer groups'),
            'required' => false,
            'values' => Ves_FormBuilder_Block_Adminhtml_Formbuilder_Edit_Tab_Form::getCustomerGroups(),
            ));

        $fieldset->addField("email_receive", "text", array(
            "label" => Mage::helper("ves_formbuilder")->__("Receive Notification"),
            "name" => "email_receive",
            "class" => "form-control required-entry",
            "required" => true,
            "note" => Mage::helper("ves_formbuilder")->__("If you use multiple separate by comma.<br/>Note: when sending to many email the load time will increase"),
            ));

        $fieldset->addField('email_template', 'select', array(
            'name' => 'email_template',
            'label' => Mage::helper('ves_formbuilder')->__('Email Template'),
            "class" => "form-control",
            "required" => false,
            'values' => Ves_FormBuilder_Block_Adminhtml_Formbuilder_Edit_Tab_Form::getEmailTemplates(),
            ));

        $fieldset->addField('show_captcha', 'select', array(
            'label' => Mage::helper('ves_formbuilder')->__('Show Captcha'),
            'options'   => array(
                '1' => Mage::helper('cms')->__('Enabled'),
                '2' => Mage::helper('cms')->__('Disabled'),
                ),
            'name' => 'show_captcha',
            "class" => "form-control",
            "required" => false,
            ));

        $fieldset->addField("success_message", "text", array(
            "label" => Mage::helper("ves_formbuilder")->__("Success Message"),
            "name" => "success_message",
            "class" => "form-control",
            "required" => false
            ));

        $fieldset->addField('show_toplink', 'select', array(
            'label' => Mage::helper('ves_formbuilder')->__('Show In Top Link'),
            'options'   => array(
                '1' => Mage::helper('cms')->__('Enabled'),
                '2' => Mage::helper('cms')->__('Disabled'),
                ),
            'name' => 'show_toplink',
            "class" => "form-control",
            "required" => false,
            ));

        $fieldset->addField("submit_button_text", "text", array(
            "label" => Mage::helper("ves_formbuilder")->__("Submit Button Text"),
            "name" => "submit_button_text",
            "class" => "form-control",
            "required" => false
            ));

        $fieldset->addField("redirect_link", "text", array(
            "label" => Mage::helper("ves_formbuilder")->__("Redirect Link"),
            "name" => "redirect_link",
            "note" => Mage::helper("ves_formbuilder")->__("Input a link url which redirect after submit form. For example: http://thedomain.com/thank.html")
            ));

        $fieldset->addField('before_form_content', 'editor', array(
            'label'     => Mage::helper('ves_formbuilder')->__('Before Form Content'),
            'class'     => '',
            'required'  => false,
            'name'      => 'before_form_content',
            'style'     => 'width:600px;height:200px;',
            'wysiwyg'   => true,
            'config'    => $config
            ));
        $fieldset->addField('after_form_content', 'editor', array(
            'label'     => Mage::helper('ves_formbuilder')->__('After Form Content'),
            'class'     => '',
            'required'  => false,
            'name'      => 'after_form_content',
            'style'     => 'width:600px;height:300px;',
            'wysiwyg'   => true,
            'config'   => $config
            ));

        $fieldset->addField("custom_template", "text", array(
            "label" => Mage::helper("ves_formbuilder")->__("Custom Form Template"),
            "name" => "custom_template",
            "note" => Mage::helper("ves_formbuilder")->__("Input the custom form template phtml file. Default: ves/formbuilder/default.phtml <br/> Empty to get default template.")
            ));


        if (Mage::getSingleton("adminhtml/session")->getBlockData()) {
            $form->setValues(Mage::getSingleton("adminhtml/session")->getBlockData());
            Mage::getSingleton("adminhtml/session")->getBlockData(null);
        } elseif ($model) {
            $form->setValues($model->getData());
        }

        return parent::_prepareForm();
    }
    static public function getCustomerGroups()
    {
        $data_array = array();
        $customer_groups = Mage::getModel('customer/group')->getCollection();;

        foreach ($customer_groups as $item_group) {
            $data_array[] = array('value' => $item_group->getCustomerGroupId(), 'label' => $item_group->getData('customer_group_code'));
        }
        return ($data_array);
    }

    static public function getEmailTemplates()
    {
        if(!$collection = Mage::registry('config_system_email_template')) {
            $collection = Mage::getResourceModel('core/email_template_collection')
            ->load();

            Mage::register('config_system_email_template', $collection);
        }
        $options = $collection->toOptionArray();
        $templateName = Mage::helper('adminhtml')->__('Default Template from Locale');
        $nodeName = 'custom_forms_email_template';

        $templateName = Mage::helper('adminhtml')->__("Custom Forms Email");
        $templateName = Mage::helper('adminhtml')->__('%s (Default Template from Locale)', $templateName);

        array_unshift(
            $options,
            array(
                'value'=> $nodeName,
                'label' => $templateName
                )
            );
        return $options;
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
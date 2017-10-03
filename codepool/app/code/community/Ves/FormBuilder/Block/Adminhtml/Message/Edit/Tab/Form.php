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
class Ves_FormBuilder_Block_Adminhtml_Message_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
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
        $model = Mage::registry("message_data");

        $this->setForm($form);
        $fieldset = $form->addFieldset("message_data", array("legend" => Mage::helper("ves_formbuilder")->__("Message information")));

        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);

        if($model->getId()){
          $fieldset->addField("message_id", "hidden", array(
            "label" => Mage::helper("ves_formbuilder")->__("Id"),
            "name" => "message_id",
            ));
        }

        $fieldset->addField('message', 'note', array(
         'label' => Mage::helper('ves_formbuilder')->__('Message Detail'),
         'name' => 'message',
         'text' => $model->getMessage()
         ));
        if($model->getProductId()) {
          $fieldset->addField('product_name', 'note', array(
           'label' => Mage::helper('ves_formbuilder')->__('Product Name'),
           'name' => 'product_name',
           'text' => '<a href="'.Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/catalog_product/edit', array("id" => $model->getProductId())).'" target="_BLANK">'.$model->getProductName().'</a>'
           ));
        }

        $fieldset->addField('created', 'note', array(
         'label' => Mage::helper('ves_formbuilder')->__('Created At'),
         'name' => 'created',
         'text' => $model->getCreated()
         ));

        if($customerId = $model->getCustomerId()) {
          $fieldset_customer = $form->addFieldset("customer_data", array("legend" => Mage::helper("ves_formbuilder")->__("Customer Information")));

          $customer = Mage::getModel('customer/customer')->load($customerId);

          $fieldset_customer->addField('customer_name', 'note', array(
           'label' => Mage::helper('ves_formbuilder')->__('Customer Email'),
           'name' => 'customer_email',
           'text' => $customer->getName()
           ));

          $fieldset_customer->addField('customer_email', 'note', array(
           'label' => Mage::helper('ves_formbuilder')->__('Customer Email'),
           'name' => 'customer_email',
           'text' => '<a href="'.Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/customer/edit', array("id" => $customerId)).'" target="_BLANK">'.$customer->getEmail().'</a>'
           ));

          $fieldset_customer->addField('ip_address', 'note', array(
           'label' => Mage::helper('ves_formbuilder')->__('IP Address'),
           'name' => 'ip_address',
           'text' => $model->getIpAddress()
           ));
        }

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

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
class Ves_FormBuilder_Block_Adminhtml_Model_Upload_Form extends Ves_FormBuilder_Block_Adminhtml_Formbuilder_Abstract_Upload_Form {

	protected function _prepareForm() {
		$form = new Varien_Data_Form(array(
				'id' => 'upload_form',
				'action' => $this->getUrl('*/*/importCsv'),
				'method' => 'post',
				'enctype' => 'multipart/form-data'
		)
		);

		$fieldset = $form->addFieldset('upload_json', array('legend' => Mage::helper('ves_formbuilder')->__('Import Form Models From CSV')));

		$fieldset->addField('importfile', 'file', array(
				'label'     => Mage::helper('ves_formbuilder')->__('Upload CSV File'),
				'required'  => true,
				'name'      => 'importfile',
		));


		$fieldset->addField('submit', 'note', array(
				'type' => 'submit',
				'text' => $this->getButtonHtml(
					Mage::helper('ves_formbuilder')->__('Upload & Import'),
					"upload_form.submit();",
					'upload'
				)
		));

        $form->setUseContainer(true);
        $this->setForm($form);

		return parent::_prepareForm();

	} // end fun

}
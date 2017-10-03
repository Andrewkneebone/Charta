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
class Ves_FormBuilder_Block_Adminhtml_Model_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {

        parent::__construct();
        $this->_objectId = "model_id";
        $this->_blockGroup = "ves_formbuilder";
        $this->_controller = "adminhtml_formbuilder";
        $this->_updateButton("save", "label", Mage::helper("ves_formbuilder")->__("Save Item"));
        $this->_updateButton("save", "onclick", "saveForm()");
        $this->_updateButton("delete", "label", Mage::helper("ves_formbuilder")->__("Delete Item"));


        $this->_addButton("saveandcontinue", array(
            "label" => Mage::helper("ves_formbuilder")->__("Save And Continue Edit"),
            "onclick" => "saveAndContinueEdit()",
            "class" => "save",
        ), -100);

        $this->_addButton("duplicate", array(
            "label" => Mage::helper("ves_formbuilder")->__("Duplicate"),
            "onclick" => "duplicateBlock()",
            "class" => "save",
        ), -100);


        $this->_formScripts[] = "
                            function saveForm(){
                                editForm.submit();
                            }   
							function saveAndContinueEdit(){
								editForm.submit($('edit_form').action+'back/edit/');
							}
                            function duplicateBlock() {
                                editForm.submit($('edit_form').action+'duplicate/1/');
                            }
						";
    }

    public function getHeaderText()
    {
        if (Mage::registry("form_data") && Mage::registry("form_data")->getId()) {

            return Mage::helper("ves_formbuilder")->__("Edit Form '%s'", $this->htmlEscape(Mage::registry("form_data")->getTitle()));

        } else {

            return Mage::helper("ves_formbuilder")->__("Create Data Model Category");

        }
    }
}
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
class Ves_FormBuilder_Block_Adminhtml_Message_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId("message_tabs");
        $this->setDestElementId("edit_form");
        $this->setTitle(Mage::helper("ves_formbuilder")->__("Form Message"));
    }

    protected function _beforeToHtml()
    {
        $builder_type_label = "Message";


        $this->addTab("form_section", array(
            "label" => Mage::helper("ves_formbuilder")->__($builder_type_label." Information"),
            "title" => Mage::helper("ves_formbuilder")->__($builder_type_label." Information"),
            "content" => $this->getLayout()->createBlock("ves_formbuilder/adminhtml_message_edit_tab_form")->toHtml(),
            ));

        $this->addTab("detail_section", array(
            "label" => Mage::helper("ves_formbuilder")->__("Client Information"),
            "title" => Mage::helper("ves_formbuilder")->__("Client Information"),
            "content" => $this->getLayout()->createBlock("ves_formbuilder/adminhtml_message_edit_tab_detail")->toHtml(),
            ));

        return parent::_beforeToHtml();
    }

}

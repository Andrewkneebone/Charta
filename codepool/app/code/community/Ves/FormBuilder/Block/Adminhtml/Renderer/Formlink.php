<?php

class Ves_FormBuilder_Block_Adminhtml_Renderer_Formlink extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        return $this->_getValue($row);
    }
    protected function _getValue(Varien_Object $row)
    {
        $val = $row->getData($this->getColumn()->getIndex());
        $val = str_replace("no_selection", "", $val);
        if(empty($val)) {
            return "";
        }
        $model = Mage::getModel("ves_formbuilder/form")->load((int)$val) ;
        $form_title = "";
        if($model->getId()) {
            $form_title = $model->getTitle();
        }
        return '<a href="'.Mage::getSingleton('adminhtml/url')->getUrl('formbuilder/adminhtml_formbuilder/edit', array("id" => $val)).'" target="_BLANK">'.$form_title.'</a>';
    }
}
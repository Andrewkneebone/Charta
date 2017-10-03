<?php

class Ves_FormBuilder_Block_Adminhtml_Renderer_CustomerEmail extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        return $this->_getValue($row);
    }
    protected function _getValue(Varien_Object $row)
    {
        $trim_html = $this->getColumn()->getTrimHtml();
        $val = $row->getData($this->getColumn()->getIndex());
        $val = str_replace("no_selection", "", $val);
        if(empty($val)) {
            return "";
        }
        $val = strip_tags($val);
        $val = trim($val);
        $val = (int)$val;
        if($val) {
            $customer = Mage::getModel('customer/customer')
            ->load($val);
            if(is_object($customer)) {
                if($trim_html) {
                  return $customer->getEmail();
                } else {
                 return '<a href="'.Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/customer/edit', array("id" => $customer->getId())).'" target="_BLANK">'.$customer->getEmail().'</a>';
                }
            }
       }
       return "";
   }
}
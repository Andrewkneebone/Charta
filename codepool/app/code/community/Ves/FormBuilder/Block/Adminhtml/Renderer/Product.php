<?php

class Ves_FormBuilder_Block_Adminhtml_Renderer_Product extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
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
        $val = strip_tags($val);
        $val = trim($val);
        $val = (int)$val;
        if($val) {
            $product = Mage::getModel('catalog/product')
            ->load($val);
            if(is_object($product)) {
               return '<a href="'.Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/catalog_product/edit', array("id" => $product->getId())).'" target="_BLANK">'.$product->getName().'</a>';
           }
       }
       return "";
   }
}
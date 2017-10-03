<?php

class Ves_FormBuilder_Block_Adminhtml_Renderer_Message extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
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
        $val = substr($val, 0, 200);
        return $val."...";
    }
}
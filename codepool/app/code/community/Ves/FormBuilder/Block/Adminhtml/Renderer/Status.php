<?php

class Ves_FormBuilder_Block_Adminhtml_Renderer_Status extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
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
        $date = strtotime(date("Y-m-d", strtotime($val)));
        $current = strtotime(date("Y-m-d"));
        $datediff = $date - $current;

        $differance = floor($datediff/(60*60*24));
        if($differance==0)
        {
            return '<span class="new-label icon-new" title="'.Mage::helper("ves_formbuilder")->__('Massage New').'">'.Mage::helper("ves_formbuilder")->__('New').'</span>';
        }
        return "";
    }
}
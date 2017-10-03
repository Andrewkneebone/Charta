<?php
class Ves_FormBuilder_Block_Adminhtml_Renderer_Messagetext extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        return $this->_getValue($row);
    }
    protected function _getValue(Varien_Object $row)
    {
        $val = $row->getData("params");
        $val = str_replace("no_selection", "", $val);
        if(empty($val)) {
            return "";
        }
        $tmp_val = "";
        $params = unserialize($val);
        $submit_data = isset($params['submit_data'])?$params['submit_data']: array();
        if($submit_data) {
            foreach($submit_data as $key=>$val2) {
                $val2 = str_replace(array("<br>","<br/>"), "\n\r", $val2);
                $tmp_val .= trim($key)." : ".trim($val2)."\n\r\r";
            }
        }
       
        return strip_tags($tmp_val);
    }
}
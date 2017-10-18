<?php 
class Ecomwise_CategoryPermissions_Model_Customattribute_Source_Table extends Mage_Eav_Model_Entity_Attribute_Source_Table{
	
	const CATEGORY_PERMISSIONS_ATT_CODE = "allowed_customer_group";	
	
	public function getAllOptions($withEmpty = true, $defaultValues = false)
    {
        if($this->getAttribute()->getAttributeCode() == self::CATEGORY_PERMISSIONS_ATT_CODE){
        	return Mage::getModel("categorypermissions/category_attribute_source_categorypermissions")->getAllOptions();
        }
				
        return parent::getAllOptions($withEmpty, $defaultValues);
    }
}

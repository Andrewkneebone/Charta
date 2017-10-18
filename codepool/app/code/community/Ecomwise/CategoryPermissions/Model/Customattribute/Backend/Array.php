<?php 
class Ecomwise_CategoryPermissions_Model_Customattribute_Backend_Array extends Mage_Eav_Model_Entity_Attribute_Backend_Array{
	
	const CATEGORY_PERMISSIONS_ATT_CODE = "allowed_customer_group";
	
	public function beforeSave($object)
	{
		if($this->getAttribute()->getAttributeCode() == self::CATEGORY_PERMISSIONS_ATT_CODE){
			 $attributeCode = $this->getAttribute()->getName();
	        if ($attributeCode == 'allowed_customer_group') {
	            $data = $object->getData($attributeCode);
	            if (!is_array($data)) {
	                $data = array();
	            }
	            $object->setData($attributeCode, join(',', $data));
	        }
	        if (is_null($object->getData($attributeCode))) {
	            $object->setData($attributeCode, false);
	        }
	        return $this;
		}
		
		return parent::beforeSave($object);				
	}
	
	public function afterLoad($object) 
	{
		if($this->getAttribute()->getAttributeCode() == self::CATEGORY_PERMISSIONS_ATT_CODE){
			
			$attributeCode = $this->getAttribute()->getName();
	        if ($attributeCode == 'allowed_customer_group') {
	            $data = $object->getData($attributeCode);
	            if ($data) {
	                $object->setData($attributeCode, explode(',', $data));
	            }
	        }
	        return $this;
		}
		
        return parent::afterLoad($object);	
    }
    
    public function validate($object)
    {
    	if($this->getAttribute()->getAttributeCode() == self::CATEGORY_PERMISSIONS_ATT_CODE){
    		$attributeCode = $this->getAttribute()->getName();
	        $postDataConfig = ($object->getData('use_post_data_config'))? $object->getData('use_post_data_config') : array();
	        
	        $isUseConfig = false;
	        if ($postDataConfig) {
	            $isUseConfig = in_array($attributeCode, $postDataConfig);
	        }
	        
	        if ($this->getAttribute()->getIsRequired()) {
	            $attributeValue = $object->getData($attributeCode);
	            if ($this->getAttribute()->isValueEmpty($attributeValue)) {
	                if (is_array($attributeValue) && count($attributeValue)>0) {
	                } else {
	                    if(!$isUseConfig) {
	                        return false;
	                    }
	                }
	            }
	        }
	
	        if ($this->getAttribute()->getIsUnique()) {
	            if (!$this->getAttribute()->getEntity()->checkAttributeUniqueValue($this->getAttribute(), $object)) {
	                $label = $this->getAttribute()->getFrontend()->getLabel();
	                Mage::throwException(Mage::helper('eav')->__('The value of attribute "%s" must be unique.', $label));
	            }
	        }
	        
	        
	        return true;        	
    	}
    
    	return parent::validate($object);	
    }
} 
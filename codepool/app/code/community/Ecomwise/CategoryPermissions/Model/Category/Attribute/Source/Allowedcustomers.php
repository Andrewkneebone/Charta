<?php 
class Ecomwise_CategoryPermissions_Model_Category_Attribute_Source_CategoryPermissions extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
	public function getAllOptions()
    {
        if (!$this->_options) {
        	
        	$customer_group = Mage::getModel('customer/group');
			$allGroups  = $customer_group->getCollection();
			foreach ($allGroups as $gr){
				$this->_options[] = array(
					 'label' => $gr->getCustomerGroupCode(),
            	     'value' => $gr->getCustomerGroupCode(),
					 'selected' => true,
				 );
			}
			
        }
        return $this->_options;
    }
}
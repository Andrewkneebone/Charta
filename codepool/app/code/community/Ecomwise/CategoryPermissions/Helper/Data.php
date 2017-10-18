<?php 
class Ecomwise_CategoryPermissions_Helper_Data extends Mage_Core_Helper_Abstract
{
	function customerIsAllowed($category){
		if(!Mage::getStoreConfig('categorypermissions/general/enabled')){
			return true;
		}
		
		$groupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
		$group = Mage::getModel('customer/group')->load($groupId)->getData('customer_group_code');
		
		$allowedGroups=$category->getAllowedCustomerGroup();
		
		if(!is_array($allowedGroups)){
			if(in_array($group, explode(',', $allowedGroups))){
				return true;
			}
			else{
				return false;
			}
		}else{
			if(in_array($group,$allowedGroups)){
				return true;
			}
			else{
				return false;
			}
		}
	}
	
	function leadAway(){
		$destinationUrl = Mage::getBaseUrl().'ecomwise_not_allowed';
        $response = Mage::app()->getResponse();
        $response->setRedirect($destinationUrl, 301);
        $response->sendResponse();
	}
}
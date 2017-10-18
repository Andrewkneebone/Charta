<?php 
class Ecomwise_CategoryPermissions_Model_Observer extends Varien_Object{
	public function uninstall($observer){
		
		$allowedcustomer=Mage::getConfig()->getModuleConfig('Ecomwise_CategoryPermissions')->is('active', 'true');
		
		if(!$allowedcustomer) {
			//remove attribute from set
			$attribute = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_category','allowed_customer_group');
			$resource = Mage::getSingleton('core/resource');
			$cr = Mage::getSingleton('core/resource')->getConnection('core_read');
			$cw = Mage::getSingleton('core/resource')->getConnection('core_write');
			$table = $resource->getTableName('eav_entity_attribute');
			$q1 = $cw->query('DELETE FROM '.$table.' WHERE attribute_id = '.$attribute->getId().' ');
			
			//remove cms page
			Mage::getModel('cms/page')->load('ecomwise_not_allowed')->delete();
		}
	}
	
}
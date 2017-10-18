<?php 
$installer = $this;

$installer->startSetup(); 

$attributeModel = Mage::getModel('eav/entity_attribute')->loadByCode("catalog_category", "allowed_customer_group");
if($attributeModel->getId()){
	$attId = $attributeModel->getId();
	$installer->run("UPDATE  {$this->getTable('eav/attribute')} SET backend_model='eav/entity_attribute_backend_array' WHERE attribute_id = ".$attId.";");
	$installer->run("UPDATE  {$this->getTable('eav/attribute')} SET source_model='eav/entity_attribute_source_table' WHERE attribute_id = ".$attId.";");
}

$installer->endSetup(); 
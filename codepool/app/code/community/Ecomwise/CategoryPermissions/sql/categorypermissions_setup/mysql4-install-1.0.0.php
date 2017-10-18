<?php

$installer = $this;
$installer->startSetup();

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');


$resource = Mage::getSingleton('core/resource');
$cr = Mage::getSingleton('core/resource')->getConnection('core_read');
$cw = Mage::getSingleton('core/resource')->getConnection('core_write');


$entityTypeId     = $setup->getEntityTypeId('catalog_category');
$attributeSetId   = $setup->getDefaultAttributeSetId($entityTypeId);


$gruoptablerow = $cr->fetchRow("SELECT attribute_group_id FROM ".$resource->getTableName('eav_attribute_group')." WHERE  attribute_set_id  = ".$attributeSetId." AND attribute_group_name = 'General Information'");

if($gruoptablerow != null){
	
	$attributeGroupId = $gruoptablerow['attribute_group_id'];
}else{
	$attributeGroupId = $setup->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);
}

$setup->addAttribute('catalog_category', 'allowed_customer_group', array(
   	'label'         => 'Allowed Customer Groups',
   	'type'          => 'text',
   	'input'         => 'multiselect',
    'backend'           => 'categorypermissions/category_attribute_backend_categorypermissions',
    'input_renderer'    => 'categorypermissions/category_helper_sortby_categorypermissions_categorypermissions',
    'source'            => 'categorypermissions/category_attribute_source_categorypermissions',
    'sort_order'        => 40,
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'           => true,
    'required'          => false,
    'user_defined'      => false,
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'visible_on_front'  => false,
    'visible_in_advanced_search' => false,
    'unique'            => false,
	'group'				=> 'Category Permissions'
));



/* $setup->addAttributeToGroup(
		$entityTypeId,
		$attributeSetId,
		$attributeGroupId,
		'allowed_customer_group',
		'999'  //sort_order
); */

$customer_group = Mage::getModel('customer/group');
$allGroups  = $customer_group->getCollection();
$str='';
foreach ($allGroups as $gr){
	$str=$str.$gr->getCustomerGroupCode().',';
}
$s=substr_replace($str ,"",-1);

$attribute = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_category','allowed_customer_group');
$resource = Mage::getSingleton('core/resource');
$cr = Mage::getSingleton('core/resource')->getConnection('core_read');
$cw = Mage::getSingleton('core/resource')->getConnection('core_write');
$table = $resource->getTableName('catalog_category_entity_text');
$query=$cw->fetchRow('Select `value_id` from `'.$table.'` order by `value_id` desc limit 1');
$id=$query['value_id'];
$cats=Mage::getModel('catalog/category')->getCollection();
foreach($cats as $cat){
	$catId=$cat->load()->getId();
	$id=$id+1;
	$q1 = $cw->query('INSERT INTO '.$table.' values ('.$id.',3,'.$attribute->getId().',0,'.$catId.',"'.$s.'")');
} 

$cmsPageData = array(
    'title' => 'Not Allowed',
    'root_template' => 'one_column',
    'identifier' => 'ecomwise_not_allowed',
    'stores' => array(0),//available for all store views
    'content' => "Not allowed!"
);

if(!Mage::getModel('cms/page')->load('ecomwise_not_allowed', 'identifier')->getId())
{
	Mage::getModel('cms/page')->setData($cmsPageData)->save();
}

$installer->endSetup();

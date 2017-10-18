<?php
/*class Ecomwise_CategoryPermissions_Model_Resource_Eav_Mysql4_Setup extends Mage_Eav_Model_Entity_Setup
{
	public function getDefaultEntities()
    {
        return array(
            'catalog_category' => array(
                'entity_model'                   => 'catalog/category',
                'attribute_model'                => 'catalog/resource_eav_attribute',
                'table'                          => 'catalog/category',
                'additional_attribute_table'     => 'catalog/eav_attribute',
                'entity_attribute_collection'    => 'catalog/category_attribute_collection',
                'default_group'                  => 'General Information',
                'attributes'        => array(
                    'allowed_customer_group' => array(
                        'group'             => 'General Information',
                        'label'             => 'Allow for customer group',
                        'type'              => 'text',
                        'input'             => 'multiselect',
                        'backend'           => 'categorypermissions/category_attribute_backend_categorypermissions',
                        'input_renderer'    => 'categorypermissions/catalog_category_helper_sortby_categorypermissions_categorypermissions',
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
                        'unique'            => false
                    )
               )
           )
        );
    }
}*/
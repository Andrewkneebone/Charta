<?php
/**
 * Venustheme
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Venustheme EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.venustheme.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.venustheme.com/ for more information
 *
 * @category   Ves
 * @package    Ves_FormBuilder
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */

/**
 * Form Builder extension
 *
 * @category   Ves
 * @package    Ves_FormBuilder
 * @author     Venustheme Dev Team <venustheme@gmail.com>
 */
class Ves_FormBuilder_Model_Mysql4_Message_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    protected $_form_profile_id = 0;
    /**
     * Constructor method
     */
    protected function _construct() {
        parent::_construct();
        $this->_init('ves_formbuilder/message');
    }

    /**
     * After load processing - adds store information to the datasets
     *
     */
    protected function _beforeLoad()
    {

        parent::_beforeLoad();
    }
    /**
     * After load processing - adds store information to the datasets
     *
     */
    protected function _afterLoad()
    {
        if($this->_form_profile_id) {
            $export_fields_columns = Mage::getStoreConfig('ves_formbuilder/export_settings/export_fields_columns');
            if($export_fields_columns) {
                foreach ($this as $item) {
                    $params = $item->getData("params");
                    $params_array = unserialize($params);
                    if(isset($params_array['submit_data']) && $params_array['submit_data']){
                        foreach($params_array['submit_data'] as $key=>$val) {
                            if($key) {
                                $item->setData($key, $val);
                            }
                        }
                    }
                }
            }
            
        }
        parent::_afterLoad();
    }

    public function setFormProfileId($form_id = 0){
        $this->_form_profile_id = (int)$form_id;
        return $this;
    }
    public function checkCustomForm() {
        $this->getSelect()->join(
            array('form_table' => $this->getTable('ves_formbuilder/form')),
            'main_table.form_id = form_table.form_id',
            array()
            )
        ->where('form_table.form_id > ?', 0);
        return $this;
    }
     public function prepareExportData() {
        $this->getSelect()->join(
            array('form_table' => $this->getTable('ves_formbuilder/form')),
            'main_table.form_id = form_table.form_id',
            array("title","identifier")
            );
        return $this;
    }
    /**
     * Add Filter by store
     *
     * @param int|Mage_Core_Model_Store $store
     * @return Ves_Slider_Model_Mysql4_Post_Collection
     */
    public function addCustomerEmailFilter($keyword = "") {

        if($keyword) {
            $collection = Mage::getResourceModel('customer/customer_collection')
            ->addAttributeToSelect('email')
            ->addAttributeToSelect('entity_id')
            ->addFieldToFilter('email', array('like'=>"%{$keyword}%"));

            $customer_ids = array(0);

            if($items = $collection->getItems()) {
                foreach($items as $item) {
                    $customer_ids[] = $item->getEntityId();
                }
            }
            if($customer_ids) {
                $this->getSelect()
                ->where('main_table.customer_id IN (?)', $customer_ids);
            }

        }

        return $this;
    }

    public function addFormBuilderFilter($form_id = 0) {
        if($form_id) {
            $this->getSelect()->where('main_table.form_id = ?', (int)$form_id);
        }
        return $this;
    }

    public function joinFormProfile(){
        $this->getSelect()->join(
            array('form_table' => $this->getTable('ves_formbuilder/form')),
            'main_table.form_id = form_table.form_id',
            array("title", "identifier")
            );
        return $this;
    }
    /**
     * Add Filter by store
     *
     * @param int|Mage_Core_Model_Store $store
     * @return Ves_Slider_Model_Mysql4_Post_Collection
     */
    public function addProductNameFilter($keyword = "") {

        if($keyword) {
             // alias then field name
            $productAttributes = array('product_name' => 'name', 'product_price' => 'price', 'product_url_key' => 'url_key');
            foreach ($productAttributes as $alias => $attributeCode) {
                $tableAlias = $attributeCode . '_table';
                $attribute = Mage::getSingleton('eav/config')
                ->getAttribute(Mage_Catalog_Model_Product::ENTITY, $attributeCode);

                //Add eav attribute value
                $this->getSelect()->joinLeft(
                    array($tableAlias => $attribute->getBackendTable()),
                    "main_table.product_id = $tableAlias.entity_id AND $tableAlias.attribute_id={$attribute->getId()}",
                    array($alias => 'value')
                    );
            }
            $this->getSelect()->where("name_table.product_name LIKE ?", "%".$keyword."%");
        }

        return $this;

    }
}
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
class Ves_FormBuilder_Model_Mysql4_Form_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {
    /**
     * Constructor method
     */
    protected function _construct() {
        parent::_construct();
        $this->_init('ves_formbuilder/form');
    }

     /**
     * After load processing - adds store information to the datasets
     *
     */
     protected function _beforeLoad()
     {
         $store_id = Mage::app()->getStore()->getId();
         if($store_id){
            $this->addStoreFilter($store_id);
        }

        parent::_beforeLoad();
    }
    /**
     * After load processing - adds store information to the datasets
     *
     */
    protected function _afterLoad()
    {
        $connection = $this->getConnection();
        foreach ($this as $item) {
            $form_stores = array();

            if($item->getData('form_id')) {

              $form_stores = Mage::getResourceModel("ves_formbuilder/form")->lookupStoreIds($item->getData('form_id'));

            }

            if(count($form_stores) > 0 && ($form_stores[0] > 0)){
                $storeId = (int)$form_stores[0];
                $storeCode = Mage::app()->getStore($storeId)->getCode();
            } else {
                $stores = Mage::app()->getStores(false, true);
                $storeId = current($stores)->getId();
                $storeCode = key($stores);
            }

            $item->setData('_first_store_id', $storeId);
            $item->setData('store_code', $storeCode);
            $item->setData("store_id", $form_stores);
      }
      parent::_afterLoad();
     }

  public function addStoreFilter($store = "") {
    if (!Mage::app()->isSingleStoreMode()) {
        if ($store instanceof Mage_Core_Model_Store) {
            $store = array($store->getId());
        }

        $this->getSelect()->join(
            array('store_table' => $this->getTable('ves_formbuilder/form_store')),
            'main_table.form_id = store_table.form_id',
            array()
            )
        ->where('store_table.store_id in (?)', array(0, $store))
        ->group('main_table.form_id');
        return $this;
    }
    return $this;
}

}
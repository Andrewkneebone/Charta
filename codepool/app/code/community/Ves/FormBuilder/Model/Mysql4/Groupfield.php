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
class Ves_FormBuilder_Model_Mysql4_Groupfield extends Mage_Core_Model_Mysql4_Abstract {

    /**
     * Initialize resource model
     */
    protected function _construct() {

      $this->_init('ves_formbuilder/group_field', 'group_id');
    }

    /**
     * Load images
     */
   // public function loadImage(Mage_Core_Model_Abstract $object) {
   //     return $this->__loadImage($object);
   // }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @return Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object) {
      $select = parent::_getLoadSelect($field, $value, $object);

      return $select;
    }


    /**
     * Process page data before saving
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Cms_Model_Resource_Page
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object) {
      return parent::_beforeSave($object);
    }
    /**
     * Call-back function
     */
    protected function _beforeDelete(Mage_Core_Model_Abstract $object) {
        // Cleanup stats on brand delete
      $adapter = $this->_getReadAdapter();
        // 1. Delete brand/store
        //$adapter->delete($this->getTable('venustheme_brand/brand_store'), 'brand_id='.$object->getId());
        // 2. Delete brand/post_cat

      return parent::_beforeDelete($object);
    }
    /**
   * Assign page to store views
   *
   * @param Mage_Core_Model_Abstract $object
   */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
    // Code that flushes cache goes here
      Mage::app()->cleanCache( array(
        Mage_Core_Model_Store::CACHE_TAG,
        Mage_Cms_Model_Block::CACHE_TAG,
        Ves_FormBuilder_Model_Form::CACHE_BLOCK_TAG
        ) );

      return parent::_afterSave($object);
    }


  /**
   * Do store and category processing after loading
   *
   * @param Mage_Core_Model_Abstract $object Current form item
   */
  protected function _afterLoad(Mage_Core_Model_Abstract $object)
  {
    /*Assign settings to form columns*/
    if($settings = $object->getSettings()) {
       $settings_array = unserialize($settings);
       if($settings_array) {
          foreach($settings_array as $key=>$val) {
              $key = trim($key);
              if($key) {
                  $object->setData($key, $val);
              }
          }
       }

    }

    return parent::_afterLoad($object);
  }
}

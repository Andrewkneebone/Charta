<?php 
class Ecomwise_CategoryPermissions_Model_Catalog_Layer_Filter_Category extends Mage_Catalog_Model_Layer_Filter_Category {	
	
	/**
	 * Get data array for building category filter items
	 *
	 * @return array
	 */
	protected function _getItemsData()
	{
		$key = $this->getLayer()->getStateKey().'_SUBCATEGORIES';
		$data = $this->getLayer()->getAggregator()->getCacheData($key);
	
		if ($data === null) {
			$categoty   = $this->getCategory();
			/** @var $categoty Mage_Catalog_Model_Categeory */
			$categories = $categoty->getChildrenCategories();
	
			$this->getLayer()->getProductCollection()
			->addCountToCategories($categories);
	
			$data = array();
			foreach ($categories as $category) {					 
				if(Mage::helper('categorypermissions')->customerIsAllowed($category) ){	// Custom Code			 
					if ($category->getIsActive() && $category->getProductCount()) {
						$data[] = array(
								'label' => Mage::helper('core')->escapeHtml($category->getName()),
								'value' => $category->getId(),
								'count' => $category->getProductCount(),
						);
					}	
				}	
			}
			$tags = $this->getLayer()->getStateTags();
			$this->getLayer()->getAggregator()->saveCacheData($data, $key, $tags);
		}
		return $data;
	}	
	
}
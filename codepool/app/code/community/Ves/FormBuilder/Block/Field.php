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
class Ves_FormBuilder_Block_Field extends Mage_Core_Block_Template
{
	/**
     * Contructor
     */
	public function __construct()
	{
		parent::__construct();
	}

	public function getCategories($category_id = 0, $max_level = 2) {
		$return = array();
		if($category_id) {
			$category  = Mage::getModel("ves_formbuilder/category")->load($category_id);
			$count_level = 0;
			if(1 == $category->getStatus()) {
				$tmp = array("id"=>$category->getId(), "label"=>$category->getTitle() );
				$model_collection = Mage::getModel("ves_formbuilder/model")->getCollection();
				$model_collection->addFieldToFilter("category_id", $category_id)
								->addFieldToFilter("status", 1)
								->setOrder("position", "ASC");
				$tmp["models"] = $model_collection;
				$return[] = $tmp;
				$count_level++;

				//get category children
				if($count_level < $max_level) {
					$return = array_merge($return, $this->getTreeCategories($category_id, $max_level, $count_level));
				}
						
			}
			
			
		}
		return $return;
	}

	public function getTreeCategories($category_id = 0, $max_level = 2, $count_level = 2) {
		$return = array();
		$collection = Mage::getModel("ves_formbuilder/category")->getCollection();
		$collection->addFieldToFilter("parent_id", $category_id)
					->addFieldToFilter("status", 1)
					->setOrder("position", "ASC")
					->getSelect()->limit(1);

		if(0 < $collection->getSize()) {
			foreach($collection as $item) {
				$tmp = array("id" => $item->getId(), "label" => $item->getTitle(), "models" => array());
				$return[] = $tmp;
				if($count_level < $max_level) {
					$return = array_merge($return, $this->getTreeCategories($item->getId(), $max_level, $count_level+1));
				}

			}
		}

		return $return;

	}


	public function getDefaultSelectUrl() {
		return Mage::getUrl('formbuilder/index/get_defaultdatas_for_select');
	}
}
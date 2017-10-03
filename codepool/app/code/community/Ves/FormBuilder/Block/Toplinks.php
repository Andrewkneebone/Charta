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
class Ves_FormBuilder_Block_Toplinks extends Mage_Core_Block_Template
{
	/**
     * Add shopping cart link to parent block
     *
     * @return Mage_Checkout_Block_Links
     */
    public function addCustomFormLinks()
    {
        $parentBlock = $this->getParentBlock();
        if ($parentBlock && Mage::helper('core')->isModuleOutputEnabled('Ves_FormBuilder') && Mage::getStoreConfig("ves_formbuilder/ves_formbuilder/show")) {
        	//get Form Collection
        	$collection = Mage::getModel("ves_formbuilder/form")->getCollection();
        	$collection->addFieldToFilter("status", 1)
        				->addFieldToFilter("show_toplink", 1);
        			
        	if($collection->getSize()) {

        		foreach($collection as $item) {
        			if(Mage::getModel("ves_formbuilder/form")->checkFormAvailable($item)) {
        				$text = $item->getTitle();
        				$top_link = Mage::getModel('core/url_rewrite')->loadByIdPath('formbuilder/index/'.$item->getId())->getRequestPath();
        				$parentBlock->removeLinkByUrl($this->getUrl($top_link));
        				$parentBlock->addLink($text, $top_link, $text, true, array(), 50, null, 'class="top-link-form"');
        			}
        		}
        	}
        }
        return $this;
    }
    
}
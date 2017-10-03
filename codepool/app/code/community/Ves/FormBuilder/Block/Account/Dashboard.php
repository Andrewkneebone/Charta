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
 * Ves FormBuilder Extension
 *
 * @category   Ves
 * @package    Ves_FormBuilder
 * @author     Venustheme Dev Team <venustheme@gmail.com>
 */
class Ves_FormBuilder_Block_Account_Dashboard extends Mage_Core_Block_Template
{
	public function __construct(){
		if(!Mage::getStoreConfig('ves_formbuilder/ves_formbuilder/show') || !Mage::getStoreConfig('ves_formbuilder/ves_formbuilder/show_in_dashboard'))
			return;
		parent::__construct();

		if(isset($attributes['template']) && $attributes['template']) {
			$my_template = $attributes['template'];
		} elseif ($this->hasData("template")) {
			$my_template = $this->getData("template");
		} else {
			$my_template = "ves/formbuilder/account/messages.phtml";
		}

		$this->setTemplate($my_template);
	}

	public function getCustomer()
	{
		return Mage::getSingleton('customer/session')->getCustomer();
	}

	public function getSubmittedMessages(){
		$customerId = $this->getCustomer()->getId();
		$collection = Mage::getModel('ves_formbuilder/message')->getCollection()
															   ->joinFormProfile();

		$collection->addFieldToFilter('customer_id',array('eq' => $customerId))->setOrder('created','DESC');
		
		return $collection;
	}

	public function getProductUrl($product_id = 0) {
		$store = Mage::app()->getStore();
		$path = Mage::getResourceModel('core/url_rewrite')
		    ->getRequestPathByIdPath('product/'.(int)$product_id, $store);

		return $store->getBaseUrl($store::URL_TYPE_WEB) . $path;
	}
	public function getFormLinkFromID($identifier){
        if($identifier !=''){
            $form_link = $identifier.'.html';
            return Mage::getUrl().$form_link;
        }
        return false;
    }

}
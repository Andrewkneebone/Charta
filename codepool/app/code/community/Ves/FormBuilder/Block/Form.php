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
class Ves_FormBuilder_Block_Form extends Mage_Core_Block_Template
{
	protected $_fields = array(
		"text" => "ves/formbuilder/fields/text.phtml",
		"website" => "ves/formbuilder/fields/website.phtml",
		"radio" => "ves/formbuilder/fields/radio.phtml",
		"dropdown" => "ves/formbuilder/fields/dropdown.phtml",
		"paragraph" => "ves/formbuilder/fields/textarea.phtml",
		"email" => "ves/formbuilder/fields/email.phtml",
		"date" => "ves/formbuilder/fields/date.phtml",
		"time" => "ves/formbuilder/fields/time.phtml",
		"checkboxes" => "ves/formbuilder/fields/checkboxes.phtml",
		"number" => "ves/formbuilder/fields/number.phtml",
		"price" => "ves/formbuilder/fields/price.phtml",
		"section_break" => "ves/formbuilder/fields/section_break.phtml",
		"address" => "ves/formbuilder/fields/address.phtml",
		"file_upload" => "ves/formbuilder/fields/file.phtml",
		"model_dropdown" => "ves/formbuilder/fields/model_dropdown.phtml",
		"subscription" => "ves/formbuilder/fields/subscription.phtml",
		"rating" => "ves/formbuilder/fields/rating.phtml",
		"google_map" => "ves/formbuilder/fields/google_map.phtml",
		"html" => "ves/formbuilder/fields/html.phtml",
		"start_group" => "ves/formbuilder/fields/start_group.phtml",
		"end_group" => "ves/formbuilder/fields/end_group.phtml",
		"group_field" => "ves/formbuilder/fields/group_field.phtml"
		);

	/**
	 * @var string $_config
	 *
	 * @access protected
	 */
	protected $_config = '';

	/**
	 * @var string $_config
	 *
	 * @access protected
	 */
	protected $_listDesc = array();

	/**
	 * @var string $_config
	 *
	 * @access protected
	 */
	protected $_show = 0;
	protected $_theme = "";
	protected $_form = null;
	protected $_banner = null;
	protected $_iswidget = false;

	/**
	 * Contructor
	 */
	public function __construct($attributes = array())
	{
		$this->convertAttributesToConfig($attributes);

		$this->_show = $this->getConfig("show");

		if(!$this->_show) return;
		/*End init meida files*/
		parent::__construct($attributes);

		//Get Form Data From Session
		$session = Mage::getSingleton("core/session",  array("name"=>"frontend"));
		$form_data = $session->getData("form_data");
		$session->unsetData("form_data");
		//Registry form data
		Mage::register('form_data', $form_data);

		$form_id = $this->getRequest()->getParam("form_id");
		if(!$form_id) {
			$form_id = $this->getConfig("form_id");
		}

		$this->_formbuilder = $formbuilder = null;
		if($form_id) {
			$formbuilder  = Mage::getModel('ves_formbuilder/form')->load( $form_id );
			$this->setCurrentForm($formbuilder);
		}

		/*Cache Block*/
		$cache_lifetime = $this->getConfig("cache_lifetime", 86400 );
		$cache_lifetime = (int)$cache_lifetime>0?$cache_lifetime: 86400;

		$this->addData(array('cache_lifetime' => $cache_lifetime));
		$cache_key = Ves_FormBuilder_Model_Form::CACHE_BLOCK_TAG;

		$magento_version = Mage::getVersion();
        $magento_version = str_replace(".","", $magento_version);

		if((int)$magento_version >= 1900) {
			$this->addCacheTag(array(
				Mage_Core_Model_Store::CACHE_TAG,
				Mage_Cms_Model_Block::CACHE_TAG,
				$cache_key
				));
		}
		/*End Cache Block*/

		$my_template = "";

		if($formbuilder && $formbuilder->getData("custom_template")) {
			$my_template = $formbuilder->getData("custom_template");
		}
		elseif($this->hasData("template")) {
			$my_template = $this->getData("template");
		} else {
			$my_template = "ves/formbuilder/default.phtml";
		}

		$this->setTemplate($my_template);

	}

	public function convertAttributesToConfig($attributes = array()) {
		if($attributes) {
			foreach($attributes as $key=>$val) {
				$this->setConfig($key, $val);
			}
		}
	}
    /**
     * Get Key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
    	$cache_key = 'VES_FORMBUILDER_WIDGET_FROM';

    	return array(
    		$cache_key,
    		$this->getNameInLayout(),
    		Mage::app()->getStore()->getId(),
    		Mage::getDesign()->getPackageName(),
    		Mage::getDesign()->getTheme('template'),
    		Mage::getSingleton('customer/session')->getCustomerGroupId(),
    		'template' => $this->getTemplate(),
    		);
    }

    protected function _prepareLayout()
    {
    	if($this->_iswidget){
    		return parent::_prepareLayout();
    	}
    	$formbuilder = $this->getCurrentForm();
    	if ($this->getPageTitle()) {
    		$title = $this->getPageTitle();
    	} else {
    		$title = $this->__($formbuilder->getTitle());
    	}
    	$this->getLayout()->getBlock('head')->setTitle($title);
    	return parent::_prepareLayout();
    }

    public function setCurrentForm($form){
    	$this->_form = $form;
    	return $this;
    }

    public function getCurrentForm(){
    	if(!$this->_form) {
    		$this->_form = Mage::registry("current_form");
    	}
    	return $this->_form;
    }

    public function _toHtml(){

    	$this->_formbuilder = $this->getCurrentForm();

    	if($this->_formbuilder && !Mage::getModel('ves_formbuilder/form')->checkFormAvailable($this->_formbuilder)) {
    		$this->_formbuilder = null;
    	}

    	$this->setCustomForm(null);

    	if($this->_formbuilder) {
    		$before_form_content = $this->_formbuilder->getBeforeFormContent();
    		if($before_form_content) {
    			$processor = Mage::helper('cms')->getPageTemplateProcessor();
    			$before_form_content = $processor->filter($before_form_content);
    		}
    		$this->_formbuilder->setBeforeFormContent($before_form_content);
			//
    		$after_form_content = $this->_formbuilder->getAfterFormContent();
    		if($after_form_content) {
    			$processor = Mage::helper('cms')->getPageTemplateProcessor();
    			$after_form_content = $processor->filter($after_form_content);
    		}
    		$this->_formbuilder->setAfterFormContent($after_form_content);
			//
    		$design = $this->_formbuilder->getDesign();
    		$design = Zend_Json::decode($design);
    		$fields = array();
    		if($design) {
    			$fields = $design['fields'];
    			$this->_formbuilder->setFields($fields);
    		}
    		$settings = $this->_formbuilder->getSettings();
    		$settings = unserialize($settings);

    		$this->_formbuilder->setSettings($settings);

    		$this->setCustomForm($this->_formbuilder);

    		if($my_template = $this->_formbuilder->getData("custom_template")) {
				$this->setTemplate($my_template);
			}

			$custom_css = $this->_formbuilder->getData("custom_css");
			$custom_js = $this->_formbuilder->getData("custom_js");

			$this->assign("custom_css", $custom_css);
            $this->assign("custom_js", $custom_js);
    	}
    	return parent::_toHtml();
    }

	/**
	 * get value of the extension's configuration
	 *
	 * @return string
	 */
	function getConfig( $key, $default = "", $panel='ves_formbuilder' ){

		$return = "";
		$value = $this->getData($key);
	    //Check if has widget config data
		if($this->hasData($key) && $value !== null) {

			if($value == "true") {
				return 1;
			} elseif($value == "false") {
				return 0;
			}

			return $value;

		} else {

			if(isset($this->_config[$key])){
				$return = $this->_config[$key];
			}else{
				$return = Mage::getStoreConfig("ves_formbuilder/$panel/$key");
			}
			if($return == "" && $default) {
				$return = $default;
			}

		}

		return $return;
	}

	/**
     * overrde the value of the extension's configuration
     *
     * @return string
     */
	function setConfig($key, $value) {
		if($value == "true") {
			$value =  1;
		} elseif($value == "false") {
			$value = 0;
		}
		if($value != "") {
			$this->_config[$key] = $value;
		}
		return $this;
	}

	public function getReCaptcha($call_back = false){
		if(!$this->getConfig("enabled","","recaptcha")) return;
		return Mage::helper('ves_formbuilder/recaptcha')
				->setKeys( $this->getConfig('private_key','','recaptcha'), $this->getConfig('public_key','','recaptcha') )
				->setTheme( $this->getConfig('theme','','recaptcha') )
				->setLang( $this->getConfig('lang','','recaptcha') )
				->setCallBack( $call_back )
				->getReCapcha();
	}

	public function getLayoutPath($filepath = "") {
		$current_theme_path = Mage::getSingleton('core/design_package')->getBaseDir(array('_area' => 'frontend', '_type'=>'template'));
		$current_theme_path .= "/ves_formbuilder/";

		$load_file_path = $current_theme_path.$filepath;

		if(file_exists($load_file_path)) {
			return $load_file_path;
		}
		return false;
	}

	public function getImageUrl($image = "") {
		$_imageUrl = Mage::getBaseDir('media').DS.$image;

		if (file_exists($_imageUrl)){
			return Mage::getBaseUrl("media").$image;
		}
		return false;
	}

	public function getFormAction(){
		return Mage::getUrl('formbuilder/index/post');
	}

	public function getField($field_type, $field_data){

		$fieldArr = $this->_fields;
		$html = '';
		if(array_key_exists($field_type, $fieldArr )){
			$template = $fieldArr[$field_type];
			$form_data = Mage::registry('form_data');

			$html = $this->getLayout()->createBlock('ves_formbuilder/field')
										->setData('field_data',$field_data)
										->setData('form_data', $form_data)
										->setTemplate($template)
										->toHtml();
		}
		return $html;
	}

}
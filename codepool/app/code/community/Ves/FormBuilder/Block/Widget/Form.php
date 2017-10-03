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
class Ves_FormBuilder_Block_Widget_Form extends Ves_FormBuilder_Block_Form implements Mage_Widget_Block_Interface
{
    var $_config_data = array();
	/**
	 * Contructor
	 */
	public function __construct($attributes = array())
	{
		$this->_show = $this->getConfig("show");
		if(!$this->_show) return;
		/*End init meida files*/
        $this->_iswidget = true;

        parent::__construct($attributes);
        //$html_calendar =
        //$this->setData('html_calendar',$html_calendar);
        $my_template = "";
        if(isset($attributes['template']) && $attributes['template']) {
            $my_template = $attributes['template'];
        } elseif ($this->hasData("template")) {
        	$my_template = $this->getData("template");
        } elseif(isset($attributes['block_template']) && $attributes['block_template']) {
            $my_template = $attributes['block_template'];
        } elseif ($this->hasData("block_template")) {
            $my_template = $this->getData("block_template");
        } else {
            $my_template = "ves/formbuilder/widget/default.phtml";
        }
        $this->setTemplate($my_template);

        /*Cache Block*/
        $enable_cache = $this->getConfig("enable_cache", 0 );
        if(!$enable_cache) {
            $cache_lifetime = null;
        } else {
            $cache_lifetime = $this->getConfig("cache_lifetime", 86400 );
            $cache_lifetime = (int)$cache_lifetime>0?$cache_lifetime: 86400;
        }
        $modId = rand(1,9)+rand();
        $this->setData("modId", $modId);

        $this->addData(array('cache_lifetime' => $cache_lifetime));
        $this->addCacheTag(array(
         Mage_Core_Model_Store::CACHE_TAG,
         Mage_Cms_Model_Block::CACHE_TAG,
         Ves_FormBuilder_Model_Form::CACHE_BLOCK_TAG
         ));
        /*End Cache Block*/
        
    }

    public function getHtmlCalendar(){
        $html_calendar = $this->getLayout()->createBlock('core/html_calendar')->setTemplate('page/js/calendar.phtml')->toHtml();
        return $html_calendar;
    }

	/**
     * Get Key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return array(
           'VES_BLOCKBUILDER_WIDGET_BUILDER',
           $this->getNameInLayout(),
           Mage::app()->getStore()->getId(),
           Mage::getDesign()->getPackageName(),
           Mage::getDesign()->getTheme('template'),
           Mage::getSingleton('customer/session')->getCustomerGroupId(),
           'template' => $this->getTemplate(),
           );
    }


    public function _toHtml() {

        $this->_show = $this->getConfig("show");
        if(!$this->_show) return;

        if(!$this->getCurrentForm()) {
            $code = null;
            $form_id = $this->getConfig("form_id");
            $form_id = $form_id?$form_id:0;

            if($form_id) {
                $formbuilder  = Mage::getModel('ves_formbuilder/form')->load( $form_id );
                $this->setCurrentForm($formbuilder);
            }
        }

        return parent::_toHtml();
    }

    public function renderWidgetShortcode( $shortcode = "") {
        if($shortcode) {
            $processor = Mage::helper('cms')->getPageTemplateProcessor();
            return $processor->filter($shortcode);
        }
        return;
    }

    public function getLayoutPath($filepath = "") {
        $current_theme_path = Mage::getSingleton('core/design_package')->getBaseDir(array('_area' => 'frontend', '_type'=>'template'));
        $current_theme_path .= "/ves/formbuilder/";

        $load_file_path = $current_theme_path.$filepath;

        if(file_exists($load_file_path)) {
            return $load_file_path;
        }
        return false;
    }
}
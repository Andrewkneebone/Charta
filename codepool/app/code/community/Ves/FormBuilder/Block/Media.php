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
class Ves_FormBuilder_Block_Media extends Mage_Core_Block_Template
{
    /**
     * @var string $_config
     *
     * @access protected
     */
    protected $_config = '';

    protected $_page_settings = array();

    /**
     * Contructor
     */
    public function __construct($attributes = array())
    {
        parent::__construct($attributes);

        if($this->hasData("template") && $this->getData("template")) {
            $my_template = $this->getData("template");
        }else {
            $my_template = "ves/formbuilder/page_head.phtml";
        }
        $this->setTemplate($my_template);
    }

    public function _toHtml(){
        $current_form = Mage::registry("current_form");
        if(!$current_form){
            $form_id = $this->getRequest()->getParam("id");
            $current_form = Mage::getModel("ves_formbuilder/form")->load($form_id);
        }
        

        if($this->_page_settings) {
            $custom_css = $current_form->getCustomCss();
            $custom_js = $current_form->getCustomJs();

            $this->assign("custom_css", $custom_css);
            $this->assign("custom_js", $custom_js);
            return parent::_toHtml();
        }
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
        }else{
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
}
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
class Ves_FormBuilder_Model_Form extends Mage_Core_Model_Abstract
{
    const CACHE_BLOCK_TAG            = 'ves_formbuilder_block';
    const CACHE_PAGE_TAG             = 'ves_formbuilder_page';
    const CACHE_MEDIA_TAG            = 'ves_formbuilder_media';
    const FIELD_NAME_PREFIX          = 'vesfield_';

    protected function _construct() {
        $this->_init('ves_formbuilder/form');
    }

    public function checkFormAvailable( $block_profile = null ){
        $checked = true;
        if($block_profile) {
            if(1 != $block_profile->getStatus()) {
                $checked = false;
            } else {
                $customer_group_id = (int)Mage::getSingleton('customer/session')->getCustomerGroupId();
                $customer_group =  $block_profile->getCustomerGroup();
                $array_groups = explode(",",$customer_group);
                if($array_groups && !in_array(0, $array_groups) && !in_array($customer_group_id, $array_groups)){
                    $checked = false;
                }
            }
        }
        return $checked;
    }

    public function getCustomFormFields( $post_data = array() ) {
        if(0 < $this->getId() && $post_data) {
            $form_data = array();
            $custom_fields = array();

            $except_field_types = array("end_group");
            $emails = array();
            $is_subscription = false;
            $session            = Mage::getSingleton('core/session');

            $design = $this->getDesign();
            $design = Zend_Json::decode($design);
            if($design && isset($design['fields'])) {
                $custom_fields = $design['fields'];
            }

            if(isset($custom_fields[0]) && isset($custom_fields[0]['fields'])) {
                unset($custom_fields[0]);
            }

            if($custom_fields) {

                $processor = Mage::helper('cms')->getPageTemplateProcessor();

                foreach( $custom_fields as $i => $field) {
                    
                    $field_id = "vesfield_".$field['cid'];
                    $field_type = $field['field_type'];
                    $field_value = "";

                    if(isset($post_data[$field_id])) {
                        $tmp = $field;
                        $field_value = isset($post_data[$field_id])?$post_data[$field_id]:"";
                        switch ($field_type) {
                            case 'website':
                                $field_value = '<a href="'.$field_value.'" target="_BLANK">'.$field_value.'</a>';

                            break;
                            case 'email':
                                $emails[] = trim($field_value);
                                $is_sender_email = isset($field['is_sender_email'])?$field['is_sender_email']:0;
                                if($is_sender_email) {
                                    $tmp['sender_email'] = trim($field_value);
                                }
                                $tmp['thanks_email'] = trim($field_value);
                                $field_value = '<a href="mailto:'.trim($field_value).'" target="_BLANK">'.$field_value.'</a>';
                                
                            break;
                            case 'radio':

                                if($field_value == "other" && isset($post_data[$field_id."_other"]) && $post_data[$field_id."_other"]) {
                                    $field_value = $post_data[$field_id."_other"];
                                }
                                if(strpos($field_value, "{{") !== false) {
                                    $field_value = str_replace(array("{{", "}}"), array('<img src="{{', '}}" alt="img"/>'), $field_value);
                                    $field_value = $processor->filter($field_value);
                                }
                                
                            break;
                            case 'checkboxes':
                                if(is_array($field_value) && $field_value) {
                                    foreach($field_value as $j => $value ){
                                        if($value == "other" && isset($post_data[$field_id."_other"]) && $post_data[$field_id."_other"]) {

                                            $field_value[$j] = $post_data[$field_id."_other"];
                                        }

                                        if(strpos($field_value[$j], "{{") !== false) {
                                            $field_value[$j] = str_replace(array("{{", "}}"), array('<img src="{{', '}}" alt="img"/>'), $field_value[$j]);
                                            $field_value[$j] = $processor->filter($field_value[$j]);
                                        }
                                    }
                                }
                                $field_value = implode(", ", $field_value);
                            break;
                            case 'address':
                                $street = isset($post_data[$field_id."_street"])?$post_data[$field_id."_street"]: "";
                                $city = isset($post_data[$field_id."_city"])?$post_data[$field_id."_city"]: "";
                                $state = isset($post_data[$field_id."_state"])?$post_data[$field_id."_state"]: "";
                                $zipcode = isset($post_data[$field_id."_zipcode"])?$post_data[$field_id."_zipcode"]: "";
                                $country_code = isset($post_data[$field_id."_country"])?$post_data[$field_id."_country"]: "";
                                $country = Mage::getModel('directory/country')->loadByCode($country_code);
                                $country_name = $country->getName();
                                $field_value = $this->formatAddress($street, $city, $state, $zipcode, $country_name);
                            break;
                            case 'file_upload':
                                $field_value = '<a href="'.$post_data[$field_id."_fileurl"].'" target="_BLANK">'.$post_data[$field_id."_filename"].' - ('.round($post_data[$field_id."_filesize"],2).'Kb)'.'</a>';
                            break;
                            case 'model_dropdown':
                                if($field_value && is_array($field_value)) {
                                    $tmp_models = array();
                                    $k = 1;
                                    foreach($field_value as $key => $fitem) {
                                        $tmp2 = array();
                                        if(is_array($fitem)) {
                                            foreach($fitem as $k2 => $fitem2) {
                                                $tmp2[] = $fitem2;
                                            }
                                        } else {
                                            $tmp2 = array($fitem);
                                        }
                                        if($tmp2 && $fitem) {
                                            $tmp_models[] = $k.". ".implode(" > ", $tmp2 );
                                        }
                                        
                                        $k++;
                                    }
                                    $field_value = implode("<br/>", $tmp_models);
                                }

                            break;
                            case 'price':
                                $field_value = Mage::helper('core')->currency($field_value, true, false);
                            break;
                            case 'google_map':
                                $location = $field_value;
                                $lat = isset($post_data[$field_id."_lat"])?$post_data[$field_id."_lat"]: "";
                                $long = isset($post_data[$field_id."_long"])?$post_data[$field_id."_long"]: "";
                                $rand = isset($post_data[$field_id."_radius"])?$post_data[$field_id."_radius"]: "";

                                $field_value = $location."<br/>".Mage::helper("ves_formbuilder")->__("Latitude: %s", $lat)." , ".Mage::helper("ves_formbuilder")->__("Longtitude: %s", $long);
                            break;
                            case 'subscription':
                                $field_value = isset($post_data[$field_id.'0'])?$post_data[$field_id.'0']:"";
                                
                                if(is_array($field_value) && $field_value) {
                                    $field_value = $field_value[0];
                                } 
                                if($field_value == 1) {
                                    $is_subscription = true;
                                }

                                $field_value = "";
                                $tmp['subscription'] = true;
                            break;
                            case 'rating':
                                $limit = isset($post_data[$field_id."_limit"])?(int)$post_data[$field_id."_limit"]: 5;
                                $rating_value = (float)$field_value;
                                if($limit) {
                                    $field_value = '<div class="rating small">';
                                    for($i=1; $i <= $limit; $i++) {
                                        $fclass = "";
                                        if($i <= $rating_value) {
                                            $fclass = 'on';
                                        }
                                        $field_value .= '<span class="star '.$fclass.'">&nbsp;</span>';
                                    }
                                    $field_value .= '<span class="score">'.Mage::helper("ves_formbuilder")->__("%s stars", $rating_value).'</span>';
                                    $field_value .= '</div>';
                                }
                            break;
                            case 'start_group':
                                
                                $field_label = isset($field['label'])?$field['label']:'';

                                if($field_label) {
                                    $field_value = '<div class="group_field" style="display:none"><strong>'.$field_label.'</strong></div>';
                                }
                            break;
                            case 'group_field':
                                //get field value from group field
                            break;
                        }
                        if(!in_array($field_type, $except_field_types)) {
                            $tmp['value'] = $field_value;
                            $form_data[] = $tmp;
                        }
                    }
                }


                /*Active Subscription For There Emails*/
                if($is_subscription && $emails) {
                    foreach($emails as $email) {
                        $status = Mage::getModel('newsletter/subscriber')->subscribe($email);
                    }
                    
                }

                return $form_data;
            }
        }
        return false;
    }

    public function buildModelFieldValue( $data = array(), $level = 0 ){
        $return = "";
        foreach($data as $key=>$val) {
            if($key == 0) {
                if($level > 0) {
                    $return .= " > ".$val;
                } else {
                    $return .= $val;
                }
                
            } else {
                $return .= $this->buildModelFieldValue( $val, $level + 1 );
            }
        }
        return $return;
    }

    public function formatAddress($street = "", $city = "", $state = "", $zipcode = "", $country = "")
    {
        $address_format = $this->getConfig("address");
        $formater   = new Varien_Filter_Template();
        $data = array("street"      => $street,
          "city"        => $city,
          "region"      => $state,
          "postcode"    => $zipcode,
          "country"     => $country);

        $formater->setVariables($data);
        return $formater->filter($address_format);
    }

    /**
     * Retrive address config object
     *
     * @return Mage_Customer_Model_Address_Config
     */
    public function getConfig($key = "", $panel = "field_templates" )
    {
        return Mage::getStoreConfig("ves_formbuilder/$panel/$key");
    }
}
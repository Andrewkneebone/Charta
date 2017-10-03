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
class Ves_FormBuilder_Helper_Data extends Mage_Core_Helper_Abstract {


    public function isAdmin()
    {
        if(Mage::app()->getStore()->isAdmin())
        {
            return true;
        }

        if(Mage::getDesign()->getArea() == 'adminhtml')
        {
            return true;
        }

        return false;
    }
    public function getWidgetFormUrl($target_id = "") {
        $params = array();
        if($target_id) {
            $params['widget_target_id'] = $target_id;
        }

        $admin_route = Mage::getConfig()->getNode('admin/routers/adminhtml/args/frontName');
        $admin_route = $admin_route?$admin_route:"admin";

        $url = Mage::getSingleton('adminhtml/url')->getUrl('*/widget/loadOptions', $params);
        $url = str_replace("/formbuilder/","/{$admin_route}/", $url);
        return $url;
    }

    public function getListWidgetsUrl($target_id = "") {
        //return Mage::helper("adminhtml")->getUrl("*/*/listwidgets");
        $params = array();
        if($target_id) {
            $params['widget_target_id'] = $target_id;
        }

        $admin_route = Mage::getConfig()->getNode('admin/routers/adminhtml/args/frontName');
        $admin_route = $admin_route?$admin_route:"admin";

        $url = Mage::getSingleton('adminhtml/url')->getUrl('*/widget/index', $params);
        $url = str_replace("/formbuilder/","/{$admin_route}/", $url);
        return $url;
    }

    public function getWidgetDataUrl() {
        return Mage::helper("adminhtml")->getUrl("*/*/widgetdata");
    }

    public function getImageUrl() {
        return str_replace(array('index.php/', 'index.php'), '', Mage::getBaseUrl('media'));
    }

    /**
     * Handles CSV upload
     * @return string $filepath
     */
    public function getUploadedFile() {
        $filepath = null;

        if(isset($_FILES['importfile']['name']) and (file_exists($_FILES['importfile']['tmp_name']))) {
            try {

                $uploader = new Varien_File_Uploader('importfile');
                $uploader->setAllowedExtensions(array('csv','txt', 'json', 'xml')); // or pdf or anything
                $uploader->setAllowRenameFiles(false);
                $uploader->setFilesDispersion(false);
                $path = Mage::helper('ves_formbuilder')->getImportPath();
                $file_type = "csv";

                if(isset($_FILES['importfile']['tmp_name']['type']) && $_FILES['importfile']['tmp_name']['type'] == "application/json") {
                    $file_type = "json";
                }

                $uploader->save($path, "ves_pagebuilder_sample_data.".$file_type);
                $filepath = $path . "ves_pagebuilder_sample_data.".$file_type;

            } catch(Exception $e) {
                // log error
                Mage::logException($e);
            } // end if

        } // end if
        return $filepath;

    }

    public function getImportPath($theme = ""){
        $path = Mage::getBaseDir('var') . DS . 'cache'.DS;

        if (is_dir_writeable($path) != true) {
            mkdir ($path, '0744', $recursive  = true );
        } // end

        return $path;
    }
    public function getAllStores() {
        $allStores = Mage::app()->getStores();
        $stores = array();
        foreach ($allStores as $_eachStoreId => $val)
        {
            $stores[]  = Mage::app()->getStore($_eachStoreId)->getId();
        }
        return $stores;
    }
    public function getIp() {

        //Just get the headers if we can or else use the SERVER global
        if ( function_exists( 'apache_request_headers' ) ) {

            $headers = apache_request_headers();

        } else {

            $headers = $_SERVER;

        }

        //Get the forwarded IP if it exists
        if ( array_key_exists( 'X-Forwarded-For', $headers ) && filter_var( $headers['X-Forwarded-For'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {

            $the_ip = $headers['X-Forwarded-For'];

        } elseif ( array_key_exists( 'HTTP_X_FORWARDED_FOR', $headers ) && filter_var( $headers['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 )
            ) {

            $the_ip = $headers['HTTP_X_FORWARDED_FOR'];

        } else {

            $the_ip = filter_var( $_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 );

        }

        return $the_ip;

    }

    public function getFormLink($form){
        $identifier = $form->getData('identifier');
        if($identifier !=''){
            $form_link = $identifier.'.html';
            return Mage::getUrl().$form_link;
        }
        return '#';
    }
}
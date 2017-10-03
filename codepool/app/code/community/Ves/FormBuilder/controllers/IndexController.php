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
class Ves_FormBuilder_IndexController extends Mage_Core_Controller_Front_Action
{
    const XML_PATH_EMAIL_SENDER     = 'ves_formbuilder/email_setting/sender_email_identity';
    const XML_PATH_NAME_SENDER      = 'ves_formbuilder/email_setting/sender_name';
    const XML_PATH_ENABLED          = 'ves_formbuilder/ves_formbuilder/show';
    const XML_PATH_EMAIL_TEMPLATE   = 'ves_formbuilder/email_setting/email_template';
    const DEFAULT_FORM_EMAIL_TEMPLATE = 'formbuilder_submit_a_custom_form';
    const DEFAULT_THANKS_EMAIL_TEMPLATE = 'formbuilder_thanksyou_notify';
    const FILE_TYPES = 'jpg,JPG,jpeg,JPEG,gif,GIF,png,PNG,doc,DOC,DOCX,docx,pdf,PDF,zip,ZIP,tar,TAR,rar,RAR,tgz,TGZ,7zip,7ZIP,gz,GZ';

    public function preDispatch()
    {

        parent::preDispatch();

        if( !Mage::getStoreConfigFlag(self::XML_PATH_ENABLED) ) {
            $this->norouteAction();
        }
    }
    /**
     * index action
     */
    public function indexAction()
    {
        $form_id = $this->getRequest()->getParam("id");
        if(!$form_id){
            $this->_redirect( "/" );
            return;
        }
        $form = Mage::getModel("ves_formbuilder/form")->load($form_id);
        Mage::register("current_form", $form);
        
        $this->loadLayout();
        $this->getLayout()->getBlock('formbuilderCustomForm')
                        ->setCurrentForm( $form )
                        ->setFormAction( Mage::getUrl('*/*/post') );
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        $this->renderLayout();
    }

    function objectToArray($d) {
        if (is_object($d)) {
            // Gets the properties of the given object
            // with get_object_vars function
            $d = get_object_vars($d);
        }
        if (is_array($d)) {
             /*
             * Return array converted to object
             * Using __FUNCTION__ (Magic constant)
             * for recursive call
             */
             return array_map(__FUNCTION__, $d);
         }
         else {
            // Return array
            return $d;
        }
    }
   protected function _parse_size($size) {
      $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
      return round($size);
    }

    public function get_defaultdatas_for_selectAction() {
        header('Content-Type: text/javascript');
        $post = $this->getRequest()->getPost();
        $data_return = 'Element.update(';
        if ( $post ) {
            $store_id = isset($post['store_id'])?$post['store_id']:0;
            $target_id = isset($post['target_id'])?$post['target_id']:"";
            $value = isset($post['value'])?(int)$post['value']:0;
            $data_return .= '"'.$target_id.'",'."'";

            if($value) {
               $collection = Mage::getModel("ves_formbuilder/model")->getCollection();
               $collection->addFieldToFilter("parent_id", $value);

               $title = Mage::helper("ves_formbuilder")->__("Select a option");
               $title = str_replace("'", "\'", $title);
               $tmp = '<option data-id="0" value="">'.Mage::helper('core')->quoteEscape( $title ).'</option>';
               $data_return .= $tmp;

               if(0 < $collection->getSize() ) {
                    foreach($collection as $item) {
                        $title = $item->getTitle();
                        $title = str_replace("'", "\'", $title);
                        $tmp = '<option data-id="'.$item->getId().'" value="'.Mage::helper('core')->quoteEscape( $title ).'">'.Mage::helper('core')->quoteEscape( $title ).'</option>';

                        $data_return .= $tmp;
                    }
               }
            }

            $data_return .= "'";
        }
        $data_return .= ')';

        echo str_replace("\n","", $data_return);
        exit;
    }

    public function postAction() {
        $post = $this->getRequest()->getPost();
        if ( $this->getRequest()->isPost() && $post ) {
            $form_id = isset($post['formId'])?$post['formId']:0;
            $model = Mage::getModel("ves_formbuilder/form")->load($form_id);
            $session = Mage::getSingleton("core/session",  array("name"=>"frontend"));
            $session->setData("form_data", $post);

            $data = $model->getData();
            $redirect_link = isset($data['redirect_link'])?$data['redirect_link']:"";
            $encrypt_form_data = Mage::getStoreConfig('ves_formbuilder/ves_formbuilder/encrypt_form_data');//Allow send encrypt base_64(serialize) submitted form data to redirect link

            $design = Zend_Json::decode($data['design']);
            $path = Mage::getBaseDir().DS.'media'.DS.'ves_formbuilder'.DS.'files'.DS;
            $has_error = false;
            $errorArr = array();

            //Validate reCaptcha
            if((1 == $model->getData("show_captcha")) && Mage::getStoreConfigFlag('ves_formbuilder/recaptcha/enabled') ){
                $check = false;
                if(isset($post['g-recaptcha-response']) && $post['g-recaptcha-response']) { //Verify recaptcha version 2
                    $resp = null;
                    // The error code from reCAPTCHA, if any
                    $recaptcha = Mage::helper('ves_formbuilder/reCaptchaV2')
                                    ->setSecretKey(Mage::getStoreConfig('ves_formbuilder/recaptcha/private_key'));
                    $resp = $recaptcha->verifyResponse(
                            $_SERVER["REMOTE_ADDR"],
                            $post['g-recaptcha-response']
                        );

                    if ($resp != null && $resp->success) {
                        $check = true;
                    }
                } else { //Verify recaptcha version 1
                    $recaptcha = Mage::helper('ves_formbuilder/recaptcha')
                                    ->setKeys( Mage::getStoreConfig('ves_formbuilder/recaptcha/private_key'),
                                       Mage::getStoreConfig("ves_formbuilder/recaptcha/public_key") )
                                    ->getReCapcha();

                    if($recaptcha) {
                        $check = $recaptcha->verify( $this->getRequest()->getParam('recaptcha_challenge_field'),
                                                    $this->getRequest()->getParam('recaptcha_response_field') )->isValid();
                        
                    }
                }
                if( !$check ){
                    Mage::getSingleton('core/session')->addError(Mage::helper('ves_formbuilder')->__("The reCAPTCHA wasn't entered correctly. Go back and try it again."));
                    $this->_redirectReferer();
                    return;
                }
            }
            //Check upload file exists
            if(isset($_FILES['form_file']['name'])){
                foreach ($_FILES['form_file']['name'] as $key => $file) {
                    if (empty($file)) {
                        continue;
                    }
                    $uploader = null;
                    try {
                        $uploader = new Mage_Core_Model_File_Uploader(
                            array(
                             'name' => $_FILES['form_file']['name'][$key],
                             'type' => $_FILES['form_file']['type'][$key],
                             'tmp_name' => $_FILES['form_file']['tmp_name'][$key],
                             'error' => $_FILES['form_file']['error'][$key],
                             'size' => $_FILES['form_file']['size'][$key]
                             ));
                    } catch (Exception $e) {
                        Mage::getSingleton('customer/session')->addError(Mage::helper('ves_formbuilder')->__($e->getMessage()));
                        $this->_redirectReferer();
                        return;
                    }
                    Mage::log('looping');
                    
                    try {
                        $file_type = self::FILE_TYPES;
                        $image_maximum_size = $this->_parse_size(@ini_get('upload_max_filesize'));
                        if($image_maximum_size <= 0) {
                            $image_maximum_size = 2;
                        }

                        $fields = $design['fields']; // Get data all field
                        $field = ''; // Get data current field
                        foreach ($fields as $k => $v) {
                            $field = '';
                            if(isset($v['cid']) && $v['cid'] == $key){
                                $field = $v;
                                if(isset($v['image_type']) && $v['image_type']){
                                    $file_type = $v['image_type'];
                                }
                                break;
                            }
                        }

                        $cid = $field['cid'];
                        $field_label = isset($field['label'])?$field['label']:'';
                        if($field && isset($field['image_maximum_size']) && $field['image_maximum_size']){
                            $image_maximum_size = $field['image_maximum_size'];
                        }

                        if( ($image_maximum_size * 1024 * 1024) < $_FILES['form_file']['size'][$key] ){
                            Mage::getSingleton('customer/session')->addError(Mage::helper('ves_formbuilder')->__($field_label.' - A file have big size.'));
                            $this->_redirectReferer();
                            return;
                        }
                        $file_type = explode(',', $file_type);
                        $uploader->setAllowedExtensions($file_type);
                        $uploader->setAllowRenameFiles(true);
                        $uploader->setFilesDispersion(false);
                        $img = $uploader->save($path, $_FILES['form_file']['name'][$key]);

                        $field_name = Ves_FormBuilder_Model_Form::FIELD_NAME_PREFIX.$cid;
                        $post[$field_name] = $field_name;
                        $post[$field_name.'_filename'] = $img['file'];
                        $post[$field_name.'_fileurl'] = Mage::getBaseUrl('media').'ves_formbuilder/files/'.$img['file'];
                        $post[$field_name.'_filesize'] = $img['size'];
                    } catch (Exception $e) {
                        Mage::getSingleton('customer/session')->addError(Mage::helper('ves_formbuilder')->__($field_label).' - '.$e->getMessage());
                        $this->_redirectReferer();
                        return;
                    }
                }
            }


            //Init email translate
            $translate = Mage::getSingleton('core/translate');
            /* @var $translate Mage_Core_Model_Translate */
            $translate->setTranslateInline(false);
            try {
                $sender_name = Mage::getStoreConfig(self::XML_PATH_NAME_SENDER);
                $sender_email = Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER);

                $error = false;

                if (!Zend_Validate::is(trim($sender_name) , 'NotEmpty')) {
                    $error = true;
                }
                if (!Zend_Validate::is(trim($sender_email) , 'NotEmpty')) {
                    $error = true;
                }
                if ($error) {
                    throw new Exception();
                }

                $product_id = isset($post['product_id'])?$post['product_id']:0;
                $post_data = array();

                //Get product information
                $post_data['product_id'] = $product_id;
                $post_data['name'] = $sender_name;
                $post_data['email'] = $sender_email;

                if($product_id) {
                    $product = Mage::getModel('catalog/product')->load((int)$product_id);
                    $post_data['product_name'] = $product->getName();
                    $post_data['product_url'] = $product->getProductUrl();
                    $post_data['product_image'] = $product->getThumbnailUrl();
                    $post_data['product_price'] = $product->getPrice();
                    $post_data['product_special_price'] = $product->getFinalPrice();
                    $post_data['product_sku'] = $product->getSku();
                }

                if (!$model->getId()) {
                    throw new Exception();
                }
                
                //Build email data object
                $custom_form_data = $model->getCustomFormFields( $post );
                $post_data["messages"] = $this->getLayout()->createBlock('ves_formbuilder/email_items')
                                        ->setCustomFormData($custom_form_data)
                                        ->toHtml();


                $postObject = new Varien_Object();
                $postObject->setData($post_data);
                // Get Store ID
                $storeId = Mage::app()->getStore()->getId();

                //Email Information
                $template_id = $model->getEmailTemplate();


                if(!$template_id || $template_id == "custom_forms_email_template"){
                    $template_id  = self::DEFAULT_FORM_EMAIL_TEMPLATE;
                }   


                $recepientEmail = $model->getEmailReceive();
                $success_message = $model->getSuccessMessage();

                //Save Custom form into message table
                $message_data = array();
                $message_data['form_id'] = $form_id;
                $message_data['product_id'] = $product_id;
                $message_data['customer_id'] = Mage::getSingleton('customer/session')->getId();
                $message_data['ip_address'] = Mage::helper('core/http')->getRemoteAddr(true);

                /*Format form data to save in message params*/
                if($custom_form_data) {
                    $form_submit_data = array();
                    foreach($custom_form_data as $key=>$val) {
                        if(isset($form_submit_data[$val['label']])) {
                            $val['label'] .= " ".$key;
                        } 
                        $form_submit_data[$val['label']] = $val['value'];
                    }
                }


                $params = array();
                $params['brower'] = $_SERVER['HTTP_USER_AGENT'];
                $params['submit_data'] = $form_submit_data; 
                $message_data['params'] = serialize($params);
                $message_data['message'] = $post_data["messages"];
                $message_data['created'] = date( 'Y-m-d H:i:s' );
                $message_data['email_from'] = $sender_email;
                //End build email data object

                $message_model = Mage::getModel("ves_formbuilder/message")->setData($message_data);
                $sender = array('name' => $sender_name, 'email' => $sender_email);
                $first_sender = $sender;
                $mailTemplate = Mage::getModel('core/email_template');
                /* @var $mailTemplate Mage_Core_Model_Email_Template */
                //Send email
                /* Custom sender email */
                foreach($custom_form_data as $item) {
                    if(isset($item['thanks_email']) && $item['thanks_email']) {
                        $first_sender['email'] = $item['thanks_email'];
                        $first_sender['name'] = $item['thanks_email'];
                        break;
                    }
                    if(isset($item['sender_email']) && $item['sender_email']){
                        $sender_email = $item['sender_email'];
                        break;
                    }
                }
                if (preg_match('/,/',$recepientEmail)){
                    $recepientEmailArr = explode(',', $recepientEmail);
                    foreach ($recepientEmailArr as $_recepientEmail) {
                        $mailTemplate->setDesignConfig(array('area' => 'frontend'))
                                    ->sendTransactional(
                                        $template_id,
                                        $sender_email,
                                        $_recepientEmail,
                                            null,
                                        array('data' => $postObject),
                                        $storeId
                                        );
                    }
                }else{
                    $mailTemplate->setDesignConfig(array('area' => 'frontend'))
                                ->sendTransactional(
                                    $template_id,
                                    $sender_email,
                                    $recepientEmail,
                                    null,
                                    array('data' => $postObject),
                                    $storeId
                                    );
                }
                $test_mode = Mage::getStoreConfig('ves_formbuilder/email_setting/enable_testmode');
                if(!$test_mode) {
                    $translate->setTranslateInline(true);
                    
                    if (!$mailTemplate->getSentSuccess()) {
                        throw new Exception();
                    }
                }
                //Save Mesage
                $message_model->save();

                $session->unsetData('form_data');
                $success_message = $success_message?$success_message:Mage::helper('ves_formbuilder')->__('Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.');

                //Send thanks you email to there email address in the custom form
                if(!$test_mode && $custom_form_data && Mage::getStoreConfig('ves_formbuilder/email_setting/send_thanks_email')) {

                    $notify_email_template = Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE);
                    if(!$notify_email_template || $notify_email_template == "ves_formbuilder_email_setting_email_template") {
                        $notify_email_template = self::DEFAULT_THANKS_EMAIL_TEMPLATE;
                    }

                    foreach($custom_form_data as $item) {
                        if(isset($item['thanks_email']) && $item['thanks_email']) {
                            $mailTemplate2 = Mage::getModel('core/email_template');
                            /* @var $mailTemplate Mage_Core_Model_Email_Template */
                            $mailTemplate2->setDesignConfig(array('area' => 'frontend'))
                                        ->sendTransactional(
                                                $notify_email_template,
                                                $sender,
                                                $item['thanks_email'],
                                                null,
                                                array('data' => $postObject, 'success_message' => $success_message),
                                                $storeId
                                            );

                            $translate->setTranslateInline(true);    
                            if (!$mailTemplate2->getSentSuccess()) {
                                throw new Exception();
                            }
                        }
                    }
                }
                //End Send email
                if($redirect_link) { //If exists redirect link, then go to the hidden link
                    if($encrypt_form_data) {
                        $redirect_link = (false === strpos($redirect_link, "?"))?$redirect_link."?":$redirect_link;
                        $redirect_link .= "&form_data=".base64_encode(serialize($form_submit_data));
                    }
                    $this->_redirectUrl($redirect_link);
                    return;
                } else {
                    Mage::getSingleton('customer/session')->addSuccess($success_message);
                    $this->_redirectReferer();
                    return;
                }
            } catch (Exception $e) {
                $translate->setTranslateInline(true);
                Mage::getSingleton('customer/session')->addError(Mage::helper('ves_formbuilder')->__('Unable to submit your request. Please, try again later'));
                $this->_redirectReferer();
                return;
            }
        } else {
            $this->_redirectReferer();
            return;
        }
    }
}
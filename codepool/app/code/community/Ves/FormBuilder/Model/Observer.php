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
class Ves_FormBuilder_Model_Observer  extends Varien_Object
{
 
  public function initControllerRouters($observer){
      if(Mage::helper("ves_formbuilder")->isAdmin()) {
          return;
      }
       $request = $observer->getEvent()->getFront()->getRequest();
       if (!Mage::app()->isInstalled()) {

        return;
      }
      $identifier = trim($request->getPathInfo(), '/');

      $condition = new Varien_Object(array(
        'identifier' => $identifier,
        'continue'   => true
        ));
      Mage::dispatchEvent('formbuilder_controller_router_match_before', array(
        'router'    => $this,
        'condition' => $condition
        ));
      $identifier = $condition->getIdentifier();
      $identifier = trim($identifier, "/");

      if ($condition->getRedirectUrl()) {
        Mage::app()->getFrontController()->getResponse()
        ->setRedirect($condition->getRedirectUrl())
        ->sendResponse();
        $request->setDispatched(true);
        return true;
      }

      if (!$condition->getContinue())
        return false;
      if($identifier) {
        $idarray = explode('/',$identifier);
        $module_enable = Mage::getStoreConfig('ves_formbuilder/ves_formbuilder/show');

        $formIdentifier = $idarray[0];

        $extension = Mage::getStoreConfig('ves_formbuilder/ves_formbuilder/extension');
        if ($extension && count($idarray) ==2 && $idarray[0] == $extension) {
          $formIdentifier = $idarray[1];
        }
        $idarray[0] = str_replace('.html', '', $formIdentifier);


        $storeId = Mage::app()->getStore()->getId();
        $form = Mage::getModel('ves_formbuilder/form')->getCollection()
        ->addFieldToFilter('identifier', $idarray[0])
        ->addFieldToFilter('status', array('eq'=>1))->getFirstItem();
        $data = $form->getData();

        if (!$data || !$module_enable) {
          return false;
        }
        if($data){
          $request->setModuleName('formbuilder')
          ->setControllerName('index')
          ->setActionName('index')
          ->setParam('id', $form->getFormId());
          $request->setAlias(
            Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
            $idarray[0]
            );
          return true;
        }
      }
      return false;
    }
    public function beforeLoadLayout(Varien_Event_Observer $observer)
    {   
      Mage::getSingleton('core/session', array('name'=>'adminhtml'));
        if (! is_null(Mage::registry("_singleton/admin/session"))) {
          if(Mage::getSingleton('admin/session')->isLoggedIn()){
            //do stuff
           return;
         }
       }
      $observer->getEvent()->getLayout()->getUpdate()->addHandle('formbuilder_add_toplinks');
    }
}
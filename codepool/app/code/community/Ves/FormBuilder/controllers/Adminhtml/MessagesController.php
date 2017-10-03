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
class Ves_FormBuilder_Adminhtml_MessagesController extends Mage_Adminhtml_Controller_Action {
    protected function _initAction() {
        $this->loadLayout()
        ->_setActiveMenu('vesextensions/formbuilder');

        return $this;
    }

    /**
     * index action
     */
    public function indexAction() {
        $form_id = $this->getRequest()->getParam("form_id");
        $form_id = (int)$form_id;
        $form = null;
        if($form_id) {
            $form = Mage::getModel("ves_formbuilder/form")->load($form_id);
            Mage::register("message_form", $form);
        }

        if($form) {
            $this->_title($this->__("Manager Messages"));
            $this->_title($this->__("Form: ").$form->getTitle());
        } else {
            $this->_title($this->__("Form Builder"));
            $this->_title($this->__("Manager Messages"));
        }
        
        $this->_initAction();
        $this->renderLayout();

    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * view and edit item action
     */
    public function editAction()
    {
        $message_id = $this->getRequest()->getParam('id');
        $model  = Mage::getModel('ves_formbuilder/message')->load($message_id);

        $this->_title($this->__("Form Builder"));
        $this->_title($this->__("Message"));
        $this->_title($this->__("View Message"));
        $field_value = Mage::helper('core')->currency("100.5", true, false);

        if ($model->getId()) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
            Mage::register('message_data', $model);
            $this->loadLayout();
            $this->_setActiveMenu('ves_formbuilder/formbuilder_message');

            $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Message Manager'),
                Mage::helper('adminhtml')->__('Message Manager')
                );
            $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('View Message'),
                Mage::helper('adminhtml')->__('View Message')
                );
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('ves_formbuilder/adminhtml_message_edit'))
            ->_addLeft($this->getLayout()->createBlock('ves_formbuilder/adminhtml_message_edit_tabs'));

            if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {

                $this->getLayout()->getBlock('head')
                ->setCanLoadTinyMce(true)
                ->addItem('js','tiny_mce/tiny_mce.js')
                ->addItem('js','mage/adminhtml/wysiwyg/tiny_mce/setup.js')
                ->addJs('mage/adminhtml/browser.js')
                ->addJs('prototype/window.js')
                ->addJs('lib/FABridge.js')
                ->addJs('lib/flex.js')
                ->addJs('mage/adminhtml/flexuploader.js')
                ->addItem('js_css','prototype/windows/themes/default.css')
                ->addCss('lib/prototype/windows/themes/magento.css');
            }

            $this->renderLayout();
        }else{
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('ves_formbuilder')->__('Message does not exist')
                );
            $this->_redirect('*/*/');
        }
    }

    public function deleteAction() {
        $id = $this->getRequest()->getParam('id');
        try {
                $_model = Mage::getModel('ves_formbuilder/message')->load($id);
                $_model->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', 1
                        )
                    );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        $this->_redirect('*/*/index');
    }

    public function massDeleteAction() {
        $IDList = $this->getRequest()->getParam('ids');
        if(!is_array($IDList)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select record(s)'));
        } else {
            try {
                foreach ($IDList as $itemId) {
                    $_model = Mage::getModel('ves_formbuilder/message')
                    ->setIsMassDelete(true)->load($itemId);
                    $_model->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($IDList)
                        )
                    );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * export grid item to XML type
     */
    public function exportXmlAction()
    {
        $fileName   = 'ves_formbuilder_message.xml';
        $content    = $this->getLayout()
                            ->createBlock('ves_formbuilder/adminhtml_message_exportgrid')
                            ->getExcelFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }


    public function exportExcelAction()  
    {  
        $fileName   = 'ves_formbuilder_message.xls';
        $content    = $this->getLayout()->createBlock('ves_formbuilder/adminhtml_message_exportgrid')->getExcelFile();
        $this->_prepareDownloadResponse($fileName, $content);  
    }  
     /**
     * Export order grid to CSV format
     */
     public function exportCsvAction()
     {
        $fileName = 'ves_formbuilder_message.csv';
        $grid = $this->getLayout()->createBlock('ves_formbuilder/adminhtml_message_exportgrid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    public function uploadCsvAction() {
        $this->loadLayout();
        $block = $this->getLayout()->createBlock('ves_formbuilder/adminhtml_message_upload');
        $this->getLayout()->getBlock('content')->append($block);
        $this->renderLayout();
    }

    public function importCsvAction(){
      $profile = $this->getRequest()->getParam('file');
      $sub_folder = $this->getRequest()->getParam('subfolder');

      $filepath = Mage::helper("ves_formbuilder")->getUploadedFile();

      if ($filepath != null) {
        try {
          $stores = Mage::helper("ves_formbuilder")->getAllStores();
          // import into model
          Mage::getSingleton('ves_formbuilder/import_message')->process($filepath, $stores);
          Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('cms')->__('CSV Imported Successfully'));
          $this->_redirect('*/*/index');

      } catch (Exception $e) {
          Mage::logException($e);
          Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cms')->__('An Error occured importing CSV.'));
          Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } // end if
        }else{
            $this->_redirect('*/*/index');
        }
    }
    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
       return Mage::getSingleton('admin/session')->isAllowed('vesextensions/formbuilder/formbuilder_message');
    }
}
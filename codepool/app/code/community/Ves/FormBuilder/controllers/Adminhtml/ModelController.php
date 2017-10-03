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
class Ves_FormBuilder_Adminhtml_ModelController extends Mage_Adminhtml_Controller_Action {
    protected function _initAction() {
        $this->loadLayout()
        ->_setActiveMenu('vesextensions/formbuilder');

        return $this;
    }

    /**
     * index action
     */
    public function indexAction() {
        $this->_title($this->__("Form Builder"));
        $this->_title($this->__("Manager Data Model"));
        $this->_initAction();
        $this->renderLayout();
    }

    public function editAction()
    {
        $this->_title($this->__("Form Builder"));
        $this->_title($this->__("Data Model"));
        $this->_title($this->__("Edit Item"));
        $id = $this->getRequest()->getParam("id");
        $model = Mage::getModel("ves_formbuilder/model")->load($id);
        if ($model->getId()) {
            Mage::register("form_data", $model);
            $this->loadLayout();
            $this->_setActiveMenu("ves_formbuilder/formbuilder");
            $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Blocks Manager"), Mage::helper("adminhtml")->__("Blocks Manager"));
            $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Blocks Description"), Mage::helper("adminhtml")->__("Blocks Description"));
            $this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock("ves_formbuilder/adminhtml_model_edit"))->_addLeft($this->getLayout()->createBlock("ves_formbuilder/adminhtml_model_edit_tabs"));
            $this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
            if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
                $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
            }
            if ($head = $this->getLayout()->getBlock('head')) {
                $head->addItem('js', 'prototype/window.js')
                ->addItem('js_css', 'prototype/windows/themes/default.css')
                ->addCss('lib/prototype/windows/themes/magento.css')
                ->addItem('js', 'mage/adminhtml/variables.js')
                ->addItem('js', 'lib/flex.js')
                ->addItem('js', 'lib/FABridge.js')
                ->addItem('js', 'mage/adminhtml/flexuploader.js')
                ->addItem('js', 'mage/adminhtml/browser.js');
            }
            $this->renderLayout();
        } else {
            Mage::getSingleton("adminhtml/session")->addError(Mage::helper("ves_formbuilder")->__("Item does not exist."));
            $this->_redirect("*/*/");
        }
    }

    public function newAction()
    {

        $this->_title($this->__("Form Builder"));
        $this->_title($this->__("Form"));
        $this->_title($this->__("New Item"));

        $id = $this->getRequest()->getParam("id");
        $model = Mage::getModel("ves_formbuilder/model")->load($id);

        $data = Mage::getSingleton("adminhtml/session")->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register("form_data", $model);

        $this->loadLayout();
        $this->_setActiveMenu("ves_formbuilder/formbuilder");

        $this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

        $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Forms Manager"), Mage::helper("adminhtml")->__("Forms Manager"));
        $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Forms Description"), Mage::helper("adminhtml")->__("Forms Description"));


        $this->_addContent($this->getLayout()->createBlock("ves_formbuilder/adminhtml_model_edit"))->_addLeft($this->getLayout()->createBlock("ves_formbuilder/adminhtml_model_edit_tabs"));

        $this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        if ($head = $this->getLayout()->getBlock('head')) {
            $head->addItem('js', 'prototype/window.js')
            ->addItem('js_css', 'prototype/windows/themes/default.css')
            ->addCss('lib/prototype/windows/themes/magento.css')
            ->addItem('js', 'mage/adminhtml/variables.js')
            ->addItem('js', 'lib/flex.js')
            ->addItem('js', 'lib/FABridge.js')
            ->addItem('js', 'mage/adminhtml/flexuploader.js')
            ->addItem('js', 'mage/adminhtml/browser.js');
        }

        $this->renderLayout();

    }

    public function saveAction()
    {

        $post_data = $this->getRequest()->getPost();

        if ($post_data) {

            try {

               
                //Duplicate Block Builder Profile
                if ($this->getRequest()->getParam("duplicate")) {
                    $model_clone = Mage::getModel('ves_formbuilder/model');
                    $model = Mage::getModel("ves_formbuilder/model")
                                    ->load($this->getRequest()->getParam("id"));

                    $block_id = 0;
                    $block_data = array('title' => $model->getTitle()."-clone",
                     'parent_id' => $model->getParentId(),
                     'category_id' => $model->getCategoryId(),
                     'status' => $model->getStatus(),
                     'position' => $model->getPosition(),
                     'created' => date( 'Y-m-d H:i:s' ));

        $model_clone->setData($block_data);

        try {
            $model_clone->save();

            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('ves_formbuilder')->__('Data Model was successfully duplicated'));
            Mage::getSingleton('adminhtml/session')->setFormData(false);

        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            Mage::getSingleton('adminhtml/session')->setFormData($block_data);
        }

        } else {

            if($this->getRequest()->getParam("id")) {
                $post_data['modified'] = date( 'Y-m-d H:i:s' );
            } else {
                $post_data['created'] = date( 'Y-m-d H:i:s' );
            }


            $model = Mage::getModel("ves_formbuilder/model")
                            ->addData($post_data)
                            ->setId($this->getRequest()->getParam("id"))
                            ->save();

            // alias  url for post
            $resroute = Mage::getStoreConfig('ves_formbuilder/ves_formbuilder/route');
            $extension = Mage::getStoreConfig('ves_formbuilder/ves_formbuilder/extension');

            Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Data Model was successfully saved"));
            Mage::getSingleton("adminhtml/session")->setFormData(false);

            if ($this->getRequest()->getParam("back")) {
                $this->_redirect("*/*/edit", array("id" => $model->getId()));
                return;
            }
        }

        $this->_redirect("*/*/");
        return;
        } catch (Exception $e) {
            Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
            Mage::getSingleton("adminhtml/session")->setFormData($this->getRequest()->getPost());
            $this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
            return;
        }

        }
        $this->_redirect("*/*/");
}

    /**
     * Delete
     */
    public function deleteAction() {

        if( $this->getRequest()->getParam('id') > 0 ) {
            try {
                $model = Mage::getModel('ves_formbuilder/model');

                $model->load($this->getRequest()->getParam('id'));

                $model->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('This Profile Was Deleted Done'));
                $this->_redirect('*/*/');

            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }
    /**
     * Export order grid to CSV format
     */
    public function exportCsvAction()
    {
        $fileName = 'ves_formmodel_profiles.csv';
        $grid = $this->getLayout()->createBlock('ves_formbuilder/adminhtml_model_exportGrid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     *  Export order grid to Excel XML format
     */
    public function exportExcelAction()
    {
        $fileName = 'ves_formmodel_profiles.xml';
        $grid = $this->getLayout()->createBlock('ves_formbuilder/adminhtml_model_exportGrid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }

    public function importCsvAction() {
         // get uploaded file
        $filepath = Mage::helper("ves_formbuilder")->getUploadedFile();

        if ($filepath != null) {

            try {
                $stores = Mage::helper("ves_formbuilder")->getAllStores();
                // import into model
                Mage::getSingleton('ves_formbuilder/import_model')->process($filepath, $stores);

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('cms')->__('CSV Imported Successfully'));

            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cms')->__('An Error occured importing CSV.'));
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } // end if
        }

        // redirect to grid page.
        $this->_redirect('*/*/index');
    }

    public function uploadCsvAction() {
        $this->loadLayout();
        $form = $this->getLayout()->createBlock('ves_formbuilder/adminhtml_model_upload');
        $this->getLayout()->getBlock('content')->append($form);
        $this->renderLayout();
    }

    public function massStatusAction() {
        $IDList = $this->getRequest()->getParam('ids');
        if(!is_array($IDList)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select record(s)'));
        } else {
            try {
                foreach ($IDList as $itemId) {
                    $_model = Mage::getSingleton('ves_formbuilder/model')
                    ->setIsMassStatus(true)
                    ->load($itemId)
                    ->setStatus($this->getRequest()->getParam('status'))
                    ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($IDList))
                    );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
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
                    $_model = Mage::getModel('ves_formbuilder/model')
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
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        $action = strtolower($this->getRequest()->getActionName());

        switch ($action) {
            case 'new':
            case 'add':
            case 'edit':
                return Mage::getSingleton('admin/session')->isAllowed('vesextensions/formbuilder/model_add');
                break;
            case 'save':
                return Mage::getSingleton('admin/session')->isAllowed('vesextensions/formbuilder/model_save');
                break;
            case 'delete':
            case 'massDelete':
                return Mage::getSingleton('admin/session')->isAllowed('vesextensions/formbuilder/model_delete');
                break;
            default:
                return Mage::getSingleton('admin/session')->isAllowed('vesextensions/formbuilder/model');
                break;
        }
    }
}
<?php
class Ecomwise_CategoryPermissions_Block_Adminhtml_Support extends Mage_Adminhtml_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{
	
	protected $_template = 'categorypermissions/support.phtml';
	
	public $module = 'Ecomwise_CategoryPermissions';
	public $supportUrl = 'http://support.ecomwise.com/support/tickets/new';
	public $email = 'feedback@ecomwise.com';
	public $faq = 'http://support.ecomwise.com/support/solutions/folders/111251';
	public $name = 'Category Permissions';
	public $compatibility = 'Magento CE 1.7-1.9.x';
	public $manualUrl = 'http://support.ecomwise.com/support/solutions/articles/69224-category-permissions';
	
	public function render(Varien_Data_Form_Element_Abstract $element) {
        return $this->toHtml();
    }
}  
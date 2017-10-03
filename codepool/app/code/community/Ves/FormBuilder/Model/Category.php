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
class Ves_FormBuilder_Model_Category extends Mage_Core_Model_Abstract
{
    const FIELD_NAME_PREFIX          = 'vesfield_';

    protected function _construct() {
        $this->_init('ves_formbuilder/category');
    }
    public function updateId($new_id = 0){
		$write = Mage::getSingleton('core/resource')->getConnection('core_write');
		$resource = Mage::getSingleton('core/resource');
		$table = $resource->getTableName('ves_formbuilder/category');
		// now $write is an instance of Zend_Db_Adapter_Abstract
		$readresult=$write->query("UPDATE ".$table." SET category_id=".$new_id." WHERE category_id = ".$this->getId());
	}
}
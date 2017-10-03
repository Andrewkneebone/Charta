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
class Ves_FormBuilder_Model_System_Config_Source_Theme {
    public function toOptionArray() {
        return array(
            array(
                'value' => 'red',
                'label' => Mage::helper("ves_formbuilder")->__('Red (default)'),
            ),
            array(
                'value' => 'white',
                'label' => Mage::helper("ves_formbuilder")->__('White'),
            ),
            array(
                'value' => 'blackglass',
                'label' => Mage::helper("ves_formbuilder")->__('Blackglass'),
            ),
            array(
                'value' => 'light',
                'label' => Mage::helper("ves_formbuilder")->__('Light'),
            ),
            array(
                'value' => 'dark',
                'label' => Mage::helper("ves_formbuilder")->__('Dark'),
            ),
            array(
                'value' => 'clean',
                'label' => Mage::helper("ves_formbuilder")->__('Clean'),
            )
        );
    }
}
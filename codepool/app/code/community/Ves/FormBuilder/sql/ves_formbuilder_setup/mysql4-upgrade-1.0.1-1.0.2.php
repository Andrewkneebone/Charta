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

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer->startSetup();
$prefix = Mage::getConfig()->getTablePrefix();
$installer->run("
-- DROP TABLE IF EXISTS `".$prefix."{$this->getTable('ves_formbuilder/model')}`;
CREATE TABLE IF NOT EXISTS `{$this->getTable('ves_formbuilder/model')}`(
	`model_id` int(11) NOT NULL AUTO_INCREMENT,
	`title` varchar(200) DEFAULT NULL,
	`parent_id` int(11) NOT NULL,
	`category_id` int(11) NOT NULL,
	`created` datetime DEFAULT NULL,
	`modified` datetime DEFAULT NULL,
	`status` tinyint(1) NOT NULL DEFAULT '1',
	`position` tinyint(4) NOT NULL DEFAULT '0',
	PRIMARY KEY (`model_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `{$this->getTable('ves_formbuilder/category')}`;
CREATE TABLE `{$this->getTable('ves_formbuilder/category')}` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `position` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

");


$installer->endSetup();


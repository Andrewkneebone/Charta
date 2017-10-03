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
$prefix = "";
$installer->run("
-- DROP TABLE IF EXISTS `".$prefix."{$this->getTable('ves_formbuilder/form')}`;
CREATE TABLE IF NOT EXISTS `{$this->getTable('ves_formbuilder/form')}`(
	`form_id` int(11) NOT NULL AUTO_INCREMENT,
	`title` varchar(200) DEFAULT NULL,
	`identifier` varchar(200) DEFAULT NULL,
	`email_receive` varchar(200) DEFAULT NULL,
	`email_template` int(11) DEFAULT NULL,
	`customer_group` varchar(100) DEFAULT NULL,
	`show_captcha` tinyint(1) NOT NULL DEFAULT '1',
	`show_toplink` tinyint(1) NOT NULL DEFAULT '0',
	`submit_button_text` varchar(200) DEFAULT NULL,
	`success_message` varchar(200) DEFAULT NULL,
	`created` datetime DEFAULT NULL,
	`modified` datetime DEFAULT NULL,
	`before_form_content` text(0) DEFAULT NULL,
	`after_form_content` text(0) DEFAULT NULL,
	`status` tinyint(1) NOT NULL DEFAULT '1',
	`design` text(0) DEFAULT NULL,
	`settings` text(0) DEFAULT NULL,
	PRIMARY KEY (`form_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `{$this->getTable('ves_formbuilder/form_store')}`;
CREATE TABLE `{$this->getTable('ves_formbuilder/form_store')}` (
  `form_id` int(11) unsigned NOT NULL,
  `store_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`form_id`,`store_id`),
  CONSTRAINT `FK_FORMBUILDER_BANNER_FORM_STORE_THEME` FOREIGN KEY (`form_id`) REFERENCES `{$this->getTable('ves_formbuilder/form')}` (`form_id`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `FK_FORMBUILDER_BANNER_FORM_STORE_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core/store')}` (`store_id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Form items to Stores';


-- DROP TABLE IF EXISTS `".$prefix."{$this->getTable('ves_formbuilder/message')}`;
CREATE TABLE IF NOT EXISTS `{$this->getTable('ves_formbuilder/message')}`(
	`message_id` int(11) NOT NULL AUTO_INCREMENT,
	`form_id` int(11) unsigned NOT NULL,
	`product_id` int(10) unsigned NOT NULL,
	`customer_id` int(10) unsigned NOT NULL,
	`subject` varchar(200) DEFAULT NULL,
	`email_from` varchar(100) DEFAULT NULL,
	`created` datetime DEFAULT NULL,
	`message`  text(0) DEFAULT NULL,
	`ip_address` varchar(100) DEFAULT NULL,
	`params` text(0) DEFAULT NULL,
	PRIMARY KEY (`message_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
");


$installer->endSetup();


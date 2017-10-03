<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();
if (!$installer->getConnection()->tableColumnExists($this->getTable('ves_formbuilder/form'), "custom_css")) {
	$installer->run("
		ALTER TABLE `{$this->getTable('ves_formbuilder/form')}` ADD COLUMN `custom_css` text(0) NULL;
		");
}
if (!$installer->getConnection()->tableColumnExists($this->getTable('ves_formbuilder/form'), "custom_js")) {
	$installer->run("
		ALTER TABLE `{$this->getTable('ves_formbuilder/form')}` ADD COLUMN `custom_js` text(0) NULL;
		");
}


$installer->run("
-- DROP TABLE IF EXISTS `{$this->getTable('ves_formbuilder/group_field')}`;
CREATE TABLE IF NOT EXISTS `{$this->getTable('ves_formbuilder/group_field')}`(
	`group_id` int(11) NOT NULL AUTO_INCREMENT,
	`title` varchar(200) DEFAULT NULL,
	`identifier` varchar(200) DEFAULT NULL,
	`created` datetime DEFAULT NULL,
	`modified` datetime DEFAULT NULL,
	`status` tinyint(1) NOT NULL DEFAULT '1',
	`design` text(0) DEFAULT NULL,
	PRIMARY KEY (`group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
");

$installer->endSetup();
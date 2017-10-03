<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();
if (!$installer->getConnection()->tableColumnExists($this->getTable('ves_formbuilder/form'), "page_title")) {
	$installer->run("
		ALTER TABLE `{$this->getTable('ves_formbuilder/form')}` ADD COLUMN `page_title` varchar(250) NULL;
		");
}
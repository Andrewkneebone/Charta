<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();
if (!$installer->getConnection()->tableColumnExists($this->getTable('ves_formbuilder/form'), "redirect_link")) {
	$installer->run("
		ALTER TABLE `{$this->getTable('ves_formbuilder/form')}` ADD COLUMN `redirect_link` varchar(250) NULL;
		");
}
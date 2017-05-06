<?php
$installer = $this;
$installer->startSetup();
$installer->run("
ALTER TABLE `{$this->getTable('siteblocks/block')}` ADD `conditions_serialized` TEXT NOT NULL AFTER `block_status`;
");
$installer->endSetup();
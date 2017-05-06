<?php
$installer = $this;
$installer->startSetup();
$installer->run("
ALTER TABLE `{$this->getTable('siteblocks/block')}` ADD `products` TEXT NOT NULL AFTER `conditions_serialized`;
");
$installer->endSetup();
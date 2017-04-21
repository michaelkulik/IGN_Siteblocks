<?php
$installer = $this;
$installer->startSetup();

$installer->run("
ALTER TABLE `{$this->getTable('siteblocks/block')}` ADD `image` TEXT NOT NUL;
");

$installer->endSetup();
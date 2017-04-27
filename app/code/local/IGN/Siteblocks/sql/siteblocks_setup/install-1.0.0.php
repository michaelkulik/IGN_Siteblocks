<?php
/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();
$table = $installer->getConnection()
    ->newTable($this->getTable('siteblocks/block'))
    ->addColumn('block_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => false,
        'primary' => true
    ))
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
        'nullable' => false
    ))
    ->addColumn('content', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => false
    ))
    ->addColumn('block_status', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
        'nullabel' => false
    ))
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        'nullable' => false
    ));
if (!$installer->getConnection()->isTableExists($this->getTable('siteblocks/block'))) {
    $installer->getConnection()->createTable($table);
}
//$installer->run("
//CREATE TABLE IF NOT EXISTS `{$this->getTable('siteblocks/block')}` (
//  `block_id` int(11) NOT NULL AUTO_INCREMENT,
//  `title` varchar(500) NOT NULL,
//  `content` text NOT NULL,
//  `block_status` tinyint(4) NOT NULL,
//  `created_at` datetime NOT NULL,
//  PRIMARY KEY (`block_id`)
//) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
//");
$installer->endSetup();
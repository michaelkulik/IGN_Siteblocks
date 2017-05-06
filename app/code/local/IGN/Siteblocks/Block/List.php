<?php

class IGN_Siteblocks_Block_List extends Mage_Core_Block_Template
{
    public function getBlocks()
    {
        // здесь просто берётся коллекция с фильтрацией по статусу (колонке block_status в БД)
        $items = Mage::getModel('siteblocks/block')->getCollection()
            ->addFieldToFilter('block_status', ['eq' => IGN_Siteblocks_Model_Source_Status::ENABLED]);
        $filteredItems = $items;

        // проверяем, выводится ли сайтблок именно на странице товара
        if (Mage::registry('current_product') instanceof Mage_Catalog_Model_Product) { // registry - вызов переменной из реестра глобальных переменных
            $filteredItems = array();
            /** @var IGN_Siteblocks_Model_Block $item*/
            foreach ($items as $item) {
                // а теперь проверяем, соответствует ли сайтблок условиям, которые в нём заданы администратором
                if ($item->validate(Mage::registry('current_product'))) {
                    $filteredItems[] = $item; // делаем массив фильтрованных блоков
                }
            }
        }
        return $filteredItems;
    }

    public function getBlockContent($block)
    {
        $processor = Mage::helper('cms')->getBlockTemplateProcessor();// используем template processor, кт может обрабатывать контент (используется для виджетов, чтобы не просто вывелся текст, а отработал нужный код)
        $html = $processor->filter($block->getContent());
        return $html;
    }

    public function getProductsList($block)
    {
        $products = $block->getProducts(); // читает позиции товаров из блока
        asort($products);
        $collection = Mage::getResourceModel('catalog/product_collection')
            ->addFieldToFilter('entity_id', ['in' => array_keys($products)])
            ->addAttributeToSelect('*');// берёт коллекцию полученных товаров
        /** @var Mage_Catalog_Block_Product_List $list */
        $list = $this->getLayout()->createBlock('catalog/product_list');
        $list->setCollection($collection);
        $list->setTemplate('siteblocks/product/list.phtml');
        return $list->toHtml();
    }
}
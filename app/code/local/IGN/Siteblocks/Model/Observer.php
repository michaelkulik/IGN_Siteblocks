<?php

class IGN_Siteblocks_Model_Observer extends Mage_Core_Model_Abstract
{
    /**
     * @param $observer Varien_Event_Observer - объект данного класса будет подаваться на вход
     */
    public function checkout_cart_product_add_after($observer)
    {
//        var_dump($observer->getEvent()->getData('quote_item')->getData());die; // quote_item - уже добавленный товар
    }
}
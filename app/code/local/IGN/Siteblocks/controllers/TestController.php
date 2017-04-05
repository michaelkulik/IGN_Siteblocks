<?php

class IGN_Siteblocks_TestController extends Mage_Core_Controller_Front_Action
{
    public function myactionAction()
    {
//        $block = Mage::getModel('siteblocks/block')->load(1);
        $my = $this->getRequest()->getParams();
        var_dump(Mage::helper('siteblocks')->isModuleOutputEnabled());
        If (Mage::helper('siteblocks')->isModuleOutputEnabled()) {
            echo 'Модуль включён';
        } else {
            echo 'Модуль выключен';
        }
    }
}
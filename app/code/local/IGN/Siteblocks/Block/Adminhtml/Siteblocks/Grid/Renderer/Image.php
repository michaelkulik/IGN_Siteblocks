<?php

class IGN_Siteblocks_Block_Adminhtml_Siteblocks_Grid_Renderer_Image
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        // При желании мы можем использовать какой-то блок с темплэйтом здесь.
        // $row - это модель нашего модуля, в данном случае текущая запись из таблицы БД,
        // поэтому мы можем обращаться как к элементу массива, например $row['title'],
        // либо $row->getImage()
        if (!$row->getImage()) {
            return 'Image was not uploaded';
        } else {
            $url = Mage::getBaseUrl('media') . 'siteblocks' . DS . $row->getImage();
            return '<img src="' . $url . '" width="100" height="auto">';
        }
    }
}

<?php

class Varien_Data_Form_Element_Myimage extends Varien_Data_Form_Element_Abstract
{
    /**
     * Constructor
     *
     * @param array $data
     */
    public function __construct($data)
    {
        parent::__construct($data);
        $this->setType('file');
    }

    /**
     * Return element html code
     *
     * @return string
     */
    public function getElementHtml()
    {
        $html = '';

        if ((string)$this->getValue()) {
            $url = $this->_getUrl();

            if( !preg_match("/^http\:\/\/|https\:\/\//", $url) ) {
                $url = Mage::getBaseUrl('media') . 'siteblocks' . DS . $url;
            }

            $html = '<a href="' . $url . '"'
                . ' onclick="imagePreview(\'' . $this->getHtmlId() . '_image\'); return false;">'
                . '<img src="' . $url . '" id="' . $this->getHtmlId() . '_image" title="' . $this->getValue() . '"'
                . ' alt="' . $this->getValue() . '" height="150" width="150" class="small-image-preview v-middle" />'
                . '</a> ';
//            $additional = Mage::app()->getLayout()->createBlock('adminhtml/template');
//            $additional->setTemplate('siteblocks/image.phtml')->setImageUrl($url);// "setImageUrl" - это наше название, задаём любое
//            $html = $additional->toHtml();
        }
        $this->setClass('input-file');
        $html .= parent::getElementHtml();

        return $html;
    }

    /**
     * Return html code of hidden element
     *
     * @return string
     */
    protected function _getHiddenInput()
    {
        return '<input type="hidden" name="' . parent::getName() . '[value]" value="' . $this->getValue() . '" />';
    }

    /**
     * Get image preview url
     *
     * @return string
     */
    protected function _getUrl()
    {
        return $this->getValue();
    }

    /**
     * Return name
     *
     * @return string
     */
    public function getName()
    {
        return  $this->getData('name');
    }
}

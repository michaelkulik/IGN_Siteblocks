<?php


class IGN_Siteblocks_Model_Source_Status
{
    // Хорошей практикой считается заводить для значений value в методах ниже константы вместо строк "1" и "0"
    const ENABLED = '1';
    const DISABLED = '0';
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => self::ENABLED, 'label'=>Mage::helper('siteblocks')->__('Enabled')),
            array('value' => self::DISABLED, 'label'=>Mage::helper('siteblocks')->__('Disabled')),
        );
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            self::DISABLED => Mage::helper('siteblocks')->__('Disabled'),
            self::ENABLED => Mage::helper('siteblocks')->__('Enabled'),
        );
    }

}
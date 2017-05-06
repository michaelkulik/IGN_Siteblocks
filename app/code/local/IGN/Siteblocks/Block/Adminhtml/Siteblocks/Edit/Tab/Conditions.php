<?php

class IGN_Siteblocks_Block_Adminhtml_Siteblocks_Edit_Tab_Conditions extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Init form
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('conditions_form');
        $this->setTitle(Mage::helper('siteblocks')->__('Block Conditions'));
    }

    protected function _prepareForm()
    {
        $model = Mage::registry('siteblocks_block');

        $form = new Varien_Data_Form();

//        $form->setHtmlIdPrefix('conditions_');

        // взяли из core/Mage/Adminhtml/controllers/Promo/CatalogController из экшна editAction()
        $model->getConditions()->setJsFormObject('block_conditions_fieldset');
        // вставили из core/Mage/Adminhtml/Block/Promo/Catalog/Edit/Tab/Conditions.php из метода _prepareForm() - начало
        $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
            ->setTemplate('promo/fieldset.phtml')
            ->setNewChildUrl($this->getUrl('*/promo_catalog/newConditionHtml/form/block_conditions_fieldset')); // ../promo_quote/.. - если используем Shopping Cart Price Rules
        $conditionsFieldset = $form->addFieldset('conditions_fieldset', array(
                'legend'=>Mage::helper('siteblocks')->__('Conditions (leave blank for all products)'))
        )->setRenderer($renderer);

        $conditionsFieldset->addField('conditions', 'text', array(
            'name' => 'conditions',
            'label' => Mage::helper('siteblocks')->__('Conditions'),
            'title' => Mage::helper('siteblocks')->__('Conditions'),
            'required' => false,
        ))->setRule($model)->setRenderer(Mage::getBlockSingleton('rule/conditions'));
        // - конец

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
    }
}
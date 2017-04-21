<?php

class IGN_Siteblocks_Block_Adminhtml_Siteblocks_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('cmsBlockGrid'); // указываем id
        $this->setDefaultSort('block_identifier'); // сортировка по умолчанию
        $this->setDefaultDir('ASC'); // направление сортировки
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('siteblocks/block')->getCollection(); // загружаем коллекцию блоков нашего модуля
        /* @var $collection Mage_Cms_Model_Mysql4_Block_Collection */
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('title', array(
            'header'    => Mage::helper('siteblocks')->__('Title'),
            'align'     => 'left',
            'index'     => 'title',
        ));

        $this->addColumn('image', [
            'header'    => Mage::helper('siteblocks')->__('Image'),
            'align'     => 'left',
            'index'     => ''
        ]);

        $this->addColumn('block_status', array(
            'header'    => Mage::helper('siteblocks')->__('Status'),
            'align'     => 'left',
            'index'     => 'block_status',
            'type'     => 'options', // тип для выпадающего списка
            // чтобы были option-ы в выпадающем списке, нужно передать массив их. Для них у нас уже реализован
            // класс /Model/Source/Status.php и нужные в нём методы, а именно метод toArray()
            'options'     => Mage::getModel('siteblocks/source_status')->toArray(),
        ));

        $this->addColumn('created_at', array(
            'header'    => Mage::helper('siteblocks')->__('Created at'),
            'index'     => 'created_at',
            'type'      => 'datetime',
        ));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('block_id');
        $this->getMassactionBlock()->setIdFieldName('block_id');
        $this->getMassactionBlock()
            ->addItem('delete', [
                'label'   => Mage::helper('siteblocks')->__('Delete'),
                'url'     => $this->getUrl('*/*/massDelete'),
                'confirm' => Mage::helper('siteblocks')->__('Are you sure you want to delete selected items?')
            ])
            ->addItem('status', [
                'label'      => Mage::helper('siteblocks')->__('Update status'),
                'url'        => $this->getUrl('*/*/massStatus'),
                'additional' =>
                    ['block_status' =>
                        [
                            'name'    => 'block_status',
                            'type'    => 'select',
                            'class'   => 'required-entry',
                            'label'   => Mage::helper('siteblocks')->__('Status'),
                            'values'  => Mage::getModel('siteblocks/source_status')->toOptionArray()
                        ]
                    ]
            ]);

        return $this;
    }

    /**
     * Row click url
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('block_id' => $row->getId()));
    }

}

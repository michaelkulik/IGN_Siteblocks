<?php

class IGN_Siteblocks_Block_Adminhtml_Siteblocks_Edit_Tab_Products_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected $_siteblock;

    public function __construct()
    {
        parent::__construct();
        $this->setId('blockProductsGrid');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
        if ($this->getSiteblock()->getId()) {
            $this->setDefaultFilter(array('in_products' => $this->_getSelectedProducts()));
        }

    }

    public function getSiteblock()
    {
        if(!$this->_siteblock) {
            $this->_siteblock = Mage::getModel('siteblocks/block')->load($this->getRequest()->getParam('block_id'));
        }
        return $this->_siteblock;
    }

    /**
     * Add filter
     *
     * @param object $column
     *
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() != 'in_products') {
            return parent::_addColumnFilterToCollection($column);
        }
        // Set custom filter for in product flag
        $productIds = $this->_getSelectedProducts();
        if (sizeof($productIds) == 0) {
            $productIds = 0;
        }

        if ($column->getFilter()->getValue()) {
            $this->getCollection()->addFieldToFilter('entity_id', array('in' => $productIds));
        } else {
            if ($productIds) {
                $this->getCollection()->addFieldToFilter('entity_id', array('nin' => $productIds));
            }
        }
        return $this;
    }

    protected function _prepareCollection()
    {
        /** @var $collection Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection */
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('type_id')
            ->addAttributeToSelect('visibility')
            ->addAttributeToSelect('status')
            ->addAttributeToSelect('price');;

        $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
        $collection->joinAttribute(
            'name',
            'catalog_product/name',
            'entity_id',
            null,
            'inner',
            $adminStore
        );
        $collection->joinAttribute(
            'custom_name',
            'catalog_product/name',
            'entity_id',
            null,
            'inner',
            $adminStore
        );
        $collection->joinAttribute(
            'status',
            'catalog_product/status',
            'entity_id',
            null,
            'inner',
            $adminStore
        );
        $collection->joinAttribute(
            'visibility',
            'catalog_product/visibility',
            'entity_id',
            null,
            'inner',
            $adminStore
        );
        $collection->joinAttribute(
            'price',
            'catalog_product/price',
            'entity_id',
            null,
            'left',
            $adminStore
        );

        $this->setCollection($collection);

        parent::_prepareCollection();
        $this->getCollection()->addWebsiteNamesToResult();
        $positions =  $this->getSelectedBlockProducts();

        foreach ($this->getCollection() as $item) {
            $position = array('position'=>'');
            if(isset($positions[$item->getId()])) {
                $position = $positions[$item->getId()];
            }
            $item->setPosition($position['position']);
        }
        return $this;
    }

    public function getSelectedBlockProducts()
    {
        $merged = array();
        $products = (array)$this->getRequest()->getPost('siteblocks_products');
        foreach ($products as $product) {
            $merged[$product] = array('position' => isset($product['position']) ? $product['position']:$product);
        }
        $productsPositions = $this->getSiteblock()->getProducts();
        foreach ($productsPositions as $productId => $position) {
            $merged[$productId] = array('position' => $position);
        }
        return $merged;
    }

    /**
     * Retrieve selected related products
     *
     * @return array
     */
    protected function _getSelectedProducts()
    {
        $products = array_keys($this->getSelectedBlockProducts());
        return $products;
    }


    protected function _prepareColumns()
    {

        $this->addColumn(
            'in_products',
            array(
                'header_css_class' => 'a-center',
                'type'             => 'checkbox',
                'name'             => 'products',
                'values'           => $this->_getSelectedProducts(),
                'align'            => 'center',
                'index'            => 'entity_id'
            )
        );

        $this->addColumn(
            'entity_id',
            array(
                'header'   => Mage::helper('catalog')->__('ID'),
                'sortable' => true,
                'width'    => 60,
                'index'    => 'entity_id'
            )
        );

        $this->addColumn(
            'name',
            array(
                'header' => Mage::helper('catalog')->__('Name'),
                'index'  => 'name'
            )
        );

        $this->addColumn(
            'type',
            array(
                'header'  => Mage::helper('catalog')->__('Type'),
                'width'   => 100,
                'index'   => 'type_id',
                'type'    => 'options',
                'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
            )
        );

        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

        $this->addColumn(
            'set_name',
            array(
                'header'  => Mage::helper('catalog')->__('Attrib. Set Name'),
                'width'   => 130,
                'index'   => 'attribute_set_id',
                'type'    => 'options',
                'options' => $sets,
            )
        );

        $this->addColumn(
            'status',
            array(
                'header'  => Mage::helper('catalog')->__('Status'),
                'width'   => 90,
                'index'   => 'status',
                'type'    => 'options',
                'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
            )
        );

        $this->addColumn(
            'visibility',
            array(
                'header'  => Mage::helper('catalog')->__('Visibility'),
                'width'   => 90,
                'index'   => 'visibility',
                'type'    => 'options',
                'options' => Mage::getSingleton('catalog/product_visibility')->getOptionArray(),
            )
        );

        $this->addColumn(
            'sku',
            array(
                'header' => Mage::helper('catalog')->__('SKU'),
                'width'  => 80,
                'index'  => 'sku'
            )
        );

        $this->addColumn(
            'price',
            array(
                'header'        => Mage::helper('catalog')->__('Price'),
                'type'          => 'currency',
                'currency_code' => (string)Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
                'index'         => 'price'
            )
        );

        $this->addColumn(
            'position',
            array(
                'header'         => Mage::helper('catalog')->__('Position'),
                'name'           => 'position',
                'type'           => 'number',
                'width'          => 6,
                'validate_class' => 'validate-number',
                'editable'       => true,
                'index'          => 'position'
            )
        );

        return parent::_prepareColumns();
    }

    /**
     * Rerieve grid URL
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/productsgrid', array('_current' => true));
    }

    public function getRowUrl($item)
    {
        return '';
    }
}

<?php

class IGN_Siteblocks_Adminhtml_SiteblocksController extends Mage_Adminhtml_Controller_Action {

    public function indexAction()
    {
        $this->loadLayout();
        $this->_addContent($this->getLayout()->createBlock('siteblocks/adminhtml_siteblocks'));
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('block_id');
        Mage::register('siteblocks_block',Mage::getModel('siteblocks/block')->load($id));
        $blockObject = (array)Mage::getSingleton('adminhtml/session')->getBlockObject(true);
        if(count($blockObject)) {
            Mage::registry('siteblocks_block')->setData($blockObject);
        }
        $this->loadLayout();
        //$this->_addLeft($this->getLayout()->createBlock('siteblocks/adminhtml_siteblocks_edit_tabs'));
        $this->_addContent($this->getLayout()->createBlock('siteblocks/adminhtml_siteblocks_edit'));
        $this->renderLayout();
    }

    protected function _uploadFile($fieldName,$model)
    {

        if( ! isset($_FILES[$fieldName])) {
            return false;
        }
        $file = $_FILES[$fieldName];

        if(isset($file['name']) && (file_exists($file['tmp_name']))){
            if($model->getId()){
                unlink(Mage::getBaseDir('media').DS.$model->getData($fieldName));
            }
            try
            {
                $path = Mage::getBaseDir('media') . DS . 'siteblocks' . DS;
                $uploader = new Varien_File_Uploader($file);
                $uploader->setAllowedExtensions(array('jpg','png','gif','jpeg'));
                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(false);

                $uploader->save($path, $file['name']);
                $model->setData($fieldName,$uploader->getUploadedFileName());
                return true;
            }
            catch(Exception $e)
            {
                return false;
            }
        }
    }

    public function saveAction()
    {
        try {
            $id = $this->getRequest()->getParam('block_id');
            /** @var IGN_Siteblocks_Model_Block $block */
            /*$block
                ->setTitle($this->getRequest()->getParam('title'))
                ->setContent($this->getRequest()->getParam('content'))
                ->setBlockStatus($this->getRequest()->getParam('block_status'))
                ->save();*/
            $block = Mage::getModel('siteblocks/block')->load($id);
            $data = $this->getRequest()->getParams();
            $links = $this->getRequest()->getPost('links', array());

            if (array_key_exists('products', $links)) {
                $selectedProducts = Mage::helper('adminhtml/js')->decodeGridSerializedInput($links['products']);
                $products = array();
                foreach($selectedProducts as $product => $position) {
                    $products[$product] = isset($position['position']) ? $position['position'] : $product;
                }
                $data['products'] = $products;
            }

            if (isset($data['rule']['conditions'])) {
                $data['conditions'] = $data['rule']['conditions'];
            }
            unset($data['rule']);
            $block
                ->loadPost($data);
            $this->_uploadFile('image',$block);
            $block
                ->setCreatedAt(Mage::app()->getLocale()->date())
                ->save();

            // сообщение об успехе
            if ($id) {
                Mage::getSingleton('adminhtml/session')->addSuccess('Block was updated successfully.');
            } else {
                Mage::getSingleton('adminhtml/session')->addSuccess('Block was created successfully!');
            }
            // если запись не сохранилась; getId() возвращает id текущей сохранённой записи
            if (!$block->getId()) {
                Mage::getSingleton('adminhtml/session')->addError('Can not be saved the siteblock.');
            }
        } catch(Exception $e) {
            Mage::logException($e);
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            Mage::getSingleton('adminhtml/session')->setBlockObject($block->getData());
            return  $this->_redirect('*/*/edit',array('block_id'=>$this->getRequest()->getParam('block_id')));
        }

        Mage::getSingleton('adminhtml/session')->addSuccess('Block was saved successfully!');

        $this->_redirect('*/*/'.$this->getRequest()->getParam('back','index'),array('block_id'=>$block->getId()));
    }

    public function deleteAction()
    {
        $block = Mage::getModel('siteblocks/block')
            ->setId($this->getRequest()->getParam('block_id'))
            ->delete();
        if($block->getId()) {
            Mage::getSingleton('adminhtml/session')->addSuccess('Block was deleted successfully!');
        }
        $this->_redirect('*/*/');

    }

    public function massStatusAction()
    {
        $statuses = $this->getRequest()->getParams();
        try {
            $blocks= Mage::getModel('siteblocks/block')
                ->getCollection()
                ->addFieldToFilter('block_id',array('in'=>$statuses['massaction']));
            foreach($blocks as $block) {
                $block->setBlockStatus($statuses['block_status'])->save();
            }
        } catch(Exception $e) {
            Mage::logException($e);
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            return $this->_redirect('*/*/');
        }
        Mage::getSingleton('adminhtml/session')->addSuccess('Blocks were updated!');

        return $this->_redirect('*/*/');

    }

    public function massDeleteAction()
    {
        $blocks = $this->getRequest()->getParams();
        try {
            $blocks= Mage::getModel('siteblocks/block')
                ->getCollection()
                ->addFieldToFilter('block_id',array('in'=>$blocks['massaction']));
            foreach($blocks as $block) {
                $block->delete();
            }
        } catch(Exception $e) {
            Mage::logException($e);
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            return $this->_redirect('*/*/');
        }
        Mage::getSingleton('adminhtml/session')->addSuccess('Blocks were deleted!');

        return $this->_redirect('*/*/');

    }

    public function productsgridAction()
    {
        $this->loadLayout()
            ->renderLayout();
    }

    public function productsAction()
    {

        $this->loadLayout()
            ->renderLayout();
    }
}

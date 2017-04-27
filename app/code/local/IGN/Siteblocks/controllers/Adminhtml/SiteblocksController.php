<?php

class IGN_Siteblocks_Adminhtml_SiteblocksController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout(); // загружаем лайаут
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
        Mage::register('siteblocks_block', Mage::getModel('siteblocks/block')->load($id));
        $blockObject = (array) Mage::getSingleton('adminhtml/session')->getBlockObject(true);
        if (count($blockObject)) {
            Mage::registry('siteblocks_block')->setData($blockObject);
        }
        $this->loadLayout();
        $this->_addContent($this->getLayout()->createBlock('siteblocks/adminhtml_siteblocks_edit'));
        $this->renderLayout();
    }

    protected function _uploadFile($fieldName, $model)
    {
        if (!isset($_FILES[$fieldName])) {
            return false;
        }
        $file = $_FILES[$fieldName];

        if (isset($file['name']) && (file_exists($file['tmp_name']))) {
            if ($model->getId()) {
                unlink(Mage::getBaseDir('media') . DS . $model->getData($fieldName));
            }
            try {
                $path = Mage::getBaseDir('media') . DS . 'siteblocks' . DS;
                $uploader = new Varien_File_Uploader($file);
                $uploader->setAllowedExtensions(['jpg', 'png', 'gif', 'jpeg']);
                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(false);

                $uploader->save($path, $file['name']);
                $model->setData($fieldName, $uploader->getUploadedFileName());
                return true;
            } catch (Exception $e) {
                return false;
            }
        }
    }

    public function saveAction()
    {
        try {
            $id = $this->getRequest()->getParam('block_id'); // на случай редактирования уже существующей записи
            $block = Mage::getModel('siteblocks/block')->load($id);
            $this->_uploadFile('image', $block);
            $block
                ->setTitle($this->getRequest()->getParam('title'))
                ->setContent($this->getRequest()->getParam('content'))
                ->setBlockStatus($this->getRequest()->getParam('block_status'))
                ->setCreatedAt(Mage::app()->getLocale()->date())
                ->save();
            // альтернатива записи выше. Запись выше используется чаще, если нужно проконтролировать каждый параметр
    //        $block
    //            ->setData($this->getRequest()->getParams())
    //            ->save();
    //        var_dump($block->getData());die; // для проверки пересылаемых данных

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
        } catch (Exception $e) {
            Mage::logException($e);
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            // сохраняем ранее введённые пользователем данные в полях на случай непредвиденной ошибки
            Mage::getSingleton('adminhtml/session')->setBlockObject($block->getData());
        }
        // данная строчка делает редирект пользователя в зависимости от того, какую кнопку он нажал
        // если save and continue, то вернёт на страницу редактирования вновь с помощью параметра back
        // если просто save, то редиректит на страницу грида, которую отрабатывает экшн index
        $this->_redirect('*/*/' . $this->getRequest()->getParam('back', 'index'), ['block_id' => $block->getId()]);
    }

    public function deleteAction()
    {
        $block = Mage::getModel('siteblocks/block')
            ->setId($this->getRequest()->getParam('block_id'))
            ->delete();
//        var_dump($block);die;
        if ($block->getId()) {
            Mage::getSingleton('adminhtml/session')->addSuccess('Block was deleted.');
        }
        $this->_redirect('*/*/'); // редирктим к гриду
    }

    public function massStatusAction()
    {
        $statuses = $this->getRequest()->getParams();
        try {
            $blocks = Mage::getModel('siteblocks/block')
                ->getCollection()
                ->addFieldToFilter('block_id', [
                    'in' => $statuses['massaction']
            ]);
            foreach ($blocks as $block) {
                $block->setBlockStatus($statuses['block_status'])->save();
            }
        } catch (Exception $e) {
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
            $blocks = Mage::getModel('siteblocks/block')
                ->getCollection()
                ->addFieldToFilter('block_id', [
                    'in' => $blocks['massaction']
            ]);
            foreach ($blocks as $block) {
                $block->delete();
            }
        } catch (Exception $e) {
            Mage::logException($e);
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            return $this->_redirect('*/*/');
        }

        Mage::getSingleton('adminhtml/session')->addSuccess('Blocks were deleted!');

        return $this->_redirect('*/*/');
    }

}
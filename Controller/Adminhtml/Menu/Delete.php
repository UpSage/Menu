<?php

 namespace UpSage\Menu\Controller\Adminhtml\Menu;
 
 use Magento\Framework\App\Action\HttpPostActionInterface;
 
 class Delete extends \UpSage\Menu\Controller\Adminhtml\Menu implements HttpPostActionInterface {
    
  public function execute() {
   $resultRedirect = $this->resultRedirectFactory->create();
   $id = $this->getRequest()->getParam('menu_id');
   if($id) {
    try {
     $model = $this->_objectManager->create(\UpSage\Menu\Model\Menu::class);
     $model->load($id);
     $model->delete();
     $this->messageManager->addSuccessMessage(__('You deleted the menu.'));
     return $resultRedirect->setPath('*/*/');
    } catch (\Exception $e) {
     $this->messageManager->addErrorMessage($e->getMessage());
     return $resultRedirect->setPath('*/*/edit', ['menu_id' => $id]);
    }
   }
   $this->messageManager->addErrorMessage(__('We can\'t find a menu to delete.'));
   return $resultRedirect->setPath('*/*/');
  }

 }

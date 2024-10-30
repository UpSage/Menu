<?php

 namespace UpSage\Menu\Controller\Adminhtml\Menu;
 
 use Magento\Framework\App\Action\HttpGetActionInterface;
 
 class Edit extends \UpSage\Menu\Controller\Adminhtml\Menu implements HttpGetActionInterface {
    
  protected $resultPageFactory;
  
  public function __construct(
   \Magento\Backend\App\Action\Context $context,
   \Magento\Framework\Registry $coreRegistry,
   \Magento\Framework\View\Result\PageFactory $resultPageFactory
  ) {
   $this->resultPageFactory = $resultPageFactory;
   parent::__construct($context, $coreRegistry);
  }
  
  public function execute() {
   $id = $this->getRequest()->getParam('menu_id');
   $model = $this->_objectManager->create(\UpSage\Menu\Model\Menu::class);
   if($id) {
    $model->load($id);
    if(!$model->getId()) {
     $this->messageManager->addErrorMessage(__('This menu no longer exists.'));
     $resultRedirect = $this->resultRedirectFactory->create();
     return $resultRedirect->setPath('*/*/');
    }
   }
   $this->_coreRegistry->register('upsage_menu', $model);
   $resultPage = $this->resultPageFactory->create();
   $this->initPage($resultPage)->addBreadcrumb(
    $id ? __('Edit Menu') : __('New Menu'),
    $id ? __('Edit Menu') : __('New Menu')
   );
   $resultPage->getConfig()->getTitle()->prepend(__('Menus'));
   $resultPage->getConfig()->getTitle()->prepend($model->getId() ? $model->getTitle() : __('New Menu'));
   return $resultPage;
  }
 
 }

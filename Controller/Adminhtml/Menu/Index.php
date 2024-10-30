<?php

 namespace UpSage\Menu\Controller\Adminhtml\Menu;
 
 use Magento\Framework\App\Action\HttpGetActionInterface;
 
 class Index extends \UpSage\Menu\Controller\Adminhtml\Menu implements HttpGetActionInterface {
    
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
   $resultPage = $this->resultPageFactory->create();
   $this->initPage($resultPage)->getConfig()->getTitle()->prepend(__('Menus'));
   $dataPersistor = $this->_objectManager->get(\Magento\Framework\App\Request\DataPersistorInterface::class);
   $dataPersistor->clear('menu_item');
   return $resultPage;
  }

 }

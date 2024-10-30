<?php

 namespace UpSage\Menu\Controller\Adminhtml\Menu;
 
 use Magento\Framework\App\Action\HttpPostActionInterface;
 use Magento\Framework\Controller\ResultFactory;
 use Magento\Backend\App\Action\Context;
 use Magento\Ui\Component\MassAction\Filter;
 use UpSage\Menu\Model\ResourceModel\Menu\CollectionFactory;
 
 class MassDelete extends \Magento\Backend\App\Action implements HttpPostActionInterface {
    
  const ADMIN_RESOURCE = 'UpSage_Menu::menu';
  
  protected $filter;
  
  protected $collectionFactory;
  
  public function __construct(Context $context, Filter $filter, CollectionFactory $collectionFactory) {
   $this->filter = $filter;
   $this->collectionFactory = $collectionFactory;
   parent::__construct($context);
  }
  
  public function execute() {
   $collection = $this->filter->getCollection($this->collectionFactory->create());
   $collectionSize = $collection->getSize();
   foreach($collection as $menu) {
    $menu->delete();
   }
   $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $collectionSize));
   $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
   return $resultRedirect->setPath('*/*/');
  }

 }

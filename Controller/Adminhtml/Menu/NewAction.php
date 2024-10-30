<?php

 namespace UpSage\Menu\Controller\Adminhtml\Menu;
 
 use Magento\Framework\App\Action\HttpGetActionInterface;
 
 class NewAction extends \UpSage\Menu\Controller\Adminhtml\Menu implements HttpGetActionInterface {
    
  protected $resultForwardFactory;
  
  public function __construct(
   \Magento\Backend\App\Action\Context $context,
   \Magento\Framework\Registry $coreRegistry,
   \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
  ) {
   $this->resultForwardFactory = $resultForwardFactory;
   parent::__construct($context, $coreRegistry);
  }
  
  public function execute() {
   $resultForward = $this->resultForwardFactory->create();
   return $resultForward->forward('edit');
  }

 }

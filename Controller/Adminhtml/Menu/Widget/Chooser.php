<?php

 namespace UpSage\Menu\Controller\Adminhtml\Menu\Widget;
 
 use Magento\Backend\App\Action\Context;
 use Magento\Framework\View\LayoutFactory;
 use Magento\Framework\Controller\Result\RawFactory;
 
 class Chooser extends \Magento\Backend\App\Action {
    
  const ADMIN_RESOURCE = 'Magento_Widget::widget_instance';
  
  protected $layoutFactory;
  
  protected $resultRawFactory;
  
  public function __construct(Context $context, LayoutFactory $layoutFactory, RawFactory $resultRawFactory) {
   $this->layoutFactory = $layoutFactory;
   $this->resultRawFactory = $resultRawFactory;
   parent::__construct($context);
  }
  
  public function execute() {
   $layout = $this->layoutFactory->create();
   $uniqId = $this->getRequest()->getParam('uniq_id');
   $pagesGrid = $layout->createBlock(
    \UpSage\Menu\Block\Adminhtml\Menu\Widget\Chooser::class,
    '',
    ['data' => ['id' => $uniqId]]
   );
   $resultRaw = $this->resultRawFactory->create();
   $resultRaw->setContents($pagesGrid->toHtml());
   return $resultRaw;
  }

 }

<?php

 namespace UpSage\Menu\Controller\Adminhtml\Menu;
 
 use Magento\Backend\App\Action\Context;
 use UpSage\Menu\Api\MenuRepositoryInterface as MenuRepository;
 use Magento\Framework\Controller\Result\JsonFactory;
 use UpSage\Menu\Api\Data\MenuInterface;
 
 class InlineEdit extends \Magento\Backend\App\Action {
    
  const ADMIN_RESOURCE = 'UpSage_Menu::menu';
  
  protected $menuRepository;
  
  protected $jsonFactory;
  
  public function __construct(
   Context $context,
   MenuRepository $menuRepository,
   JsonFactory $jsonFactory
  ) {
   parent::__construct($context);
   $this->menuRepository = $menuRepository;
   $this->jsonFactory = $jsonFactory;
  }
  
  public function execute() {
   $resultJson = $this->jsonFactory->create();
   $error = false;
   $messages = [];
   if($this->getRequest()->getParam('isAjax')) {
    $postItems = $this->getRequest()->getParam('items', []);
    if(!count($postItems)) {
     $messages[] = __('Please correct the data sent.');
     $error = true;
    } else {
     foreach(array_keys($postItems) as $menuId) {
      $menu = $this->menuRepository->getById($menuId);
      try {
       $menu->setData(array_merge($menu->getData(), $postItems[$menuId]));
       $this->menuRepository->save($menu);
      } catch (\Exception $e) {
       $messages[] = $this->getErrorWithMenuId($menu,__($e->getMessage()));
       $error = true;
      }
     }
    }
   }
   return $resultJson->setData([
    'messages' => $messages,
    'error' => $error
   ]);
  }
  
  protected function getErrorWithMenuId(MenuInterface $menu, $errorText) {
   return '[Menu ID: ' . $menu->getId() . '] ' . $errorText;
  }

 }
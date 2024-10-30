<?php

 namespace UpSage\Menu\Controller\Adminhtml\Menu;
 
 use Magento\Framework\App\Action\HttpPostActionInterface;
 use Magento\Backend\App\Action\Context;
 use UpSage\Menu\Api\MenuRepositoryInterface;
 use UpSage\Menu\Model\Menu;
 use UpSage\Menu\Model\MenuFactory;
 use Magento\Framework\App\Request\DataPersistorInterface;
 use Magento\Framework\Exception\LocalizedException;
 use Magento\Framework\Registry;
 
 class Save extends \UpSage\Menu\Controller\Adminhtml\Menu implements HttpPostActionInterface {
    
  protected $dataPersistor;
  
  private $menuFactory;
  
  private $menuRepository;
  
  public function __construct(
   Context $context,
   Registry $coreRegistry,
   DataPersistorInterface $dataPersistor,
   MenuFactory $menuFactory = null,
   MenuRepositoryInterface $menuRepository = null
  ) {
   $this->dataPersistor = $dataPersistor;
   $this->menuFactory = $menuFactory ?: \Magento\Framework\App\ObjectManager::getInstance()->get(MenuFactory::class);
   $this->menuRepository = $menuRepository ?: \Magento\Framework\App\ObjectManager::getInstance()->get(MenuRepositoryInterface::class);
   parent::__construct($context, $coreRegistry);
  }
  
  public function execute() {
   $resultRedirect = $this->resultRedirectFactory->create();
   $data = $this->getRequest()->getPostValue();
   if($data) {
    if(isset($data['is_active']) && $data['is_active'] === 'true') {
     $data['is_active'] = Menu::STATUS_ENABLED;
    }
    if(empty($data['menu_id'])) {
     $data['menu_id'] = null;
    }
    
    $model = $this->menuFactory->create();
    
    $id = $this->getRequest()->getParam('menu_id');
    if($id) {
     try {
      $model = $this->menuRepository->getById($id);
     } catch (LocalizedException $e) {
      $this->messageManager->addErrorMessage(__('This menu no longer exists.'));
      return $resultRedirect->setPath('*/*/');
     }
    }
    
    $model->setData($data);
    
    try {
     $this->menuRepository->save($model);
     $this->messageManager->addSuccessMessage(__('You saved the menu.'));
     $this->dataPersistor->clear('upsage_menu');
     return $this->processMenuReturn($model, $data, $resultRedirect);
    } catch (LocalizedException $e) {
     $this->messageManager->addErrorMessage($e->getMessage());
    } catch (\Exception $e) {
     $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the menu.'));
    }
    
    $this->dataPersistor->set('upsage_menu', $data);
    return $resultRedirect->setPath('*/*/edit', ['menu_id' => $id]);

   }
   return $resultRedirect->setPath('*/*/');
  }
  
  private function processMenuReturn($model, $data, $resultRedirect) {
   $redirect = $data['back'] ?? 'close';
   if($redirect === 'continue') {
    $resultRedirect->setPath('*/*/edit', ['menu_id' => $model->getId()]);
   } elseif ($redirect === 'close') {
    $resultRedirect->setPath('*/*/');
   } elseif ($redirect === 'duplicate') {
    $duplicateModel = $this->menuFactory->create(['data' => $data]);
    $duplicateModel->setId(null);
    $duplicateModel->setIdentifier($data['identifier'] . '-' . uniqid());
    $duplicateModel->setIsActive(Menu::STATUS_DISABLED);
    $this->menuRepository->save($duplicateModel);
    $id = $duplicateModel->getId();
    $this->messageManager->addSuccessMessage(__('You duplicated the menu.'));
    $this->dataPersistor->set('upsage_menu', $data);
    $resultRedirect->setPath('*/*/edit', ['menu_id' => $id]);
   }
   return $resultRedirect;
  }

 }

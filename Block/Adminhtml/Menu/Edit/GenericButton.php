<?php

 namespace UpSage\Menu\Block\Adminhtml\Menu\Edit;
 
 use Magento\Backend\Block\Widget\Context;
 use UpSage\Menu\Api\MenuRepositoryInterface;
 use Magento\Framework\Exception\NoSuchEntityException;
 
 class GenericButton {
    
  protected $context;
  
  protected $menuRepository;
  
  public function __construct(
   Context $context,
   MenuRepositoryInterface $menuRepository
  ) {
   $this->context = $context;
   $this->menuRepository = $menuRepository;
  }
  
  public function getMenuId() {
   try {
    return $this->menuRepository->getById(
     $this->context->getRequest()->getParam('menu_id')
    )->getId();
   } catch (NoSuchEntityException $e) {}
   return null;
  }
  
  public function getUrl($route = '', $params = []) {
   return $this->context->getUrlBuilder()->getUrl($route, $params);
  }

 }

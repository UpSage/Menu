<?php

 namespace UpSage\Menu\Controller\Adminhtml;
 
 abstract class Menu extends \Magento\Backend\App\Action {
    
  const ADMIN_RESOURCE = 'UpSage_Menu::menu';
  
  protected $_coreRegistry;
  
  public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\Registry $coreRegistry) {
   $this->_coreRegistry = $coreRegistry;
   parent::__construct($context);
  }
  
  protected function initPage($resultPage) {
   $resultPage
    ->setActiveMenu('UpSage_Menu::menu')
    ->addBreadcrumb(__('Menu'), __('Menu'))
    ->addBreadcrumb(__('Manage Menus'), __('Manage Menus'));
   return $resultPage;
  }

 }
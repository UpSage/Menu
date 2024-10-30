<?php

 declare(strict_types=1);
 
 namespace UpSage\Menu\Block\Widget;
 
 use Magento\Framework\DataObject\IdentityInterface;
 use Magento\Framework\Exception\NoSuchEntityException;
 use UpSage\Menu\Model\Menu as gMenu;
 use Magento\Widget\Block\BlockInterface;
 
 class Menu extends \Magento\Framework\View\Element\Template implements BlockInterface, IdentityInterface {
    
  protected $_filterProvider;
  
  protected static $_widgetUsageMap = [];
  
  protected $_menuFactory;
  
  private $menu;
  
  public function __construct(
   \Magento\Framework\View\Element\Template\Context $context,
   \UpSage\Menu\Model\Template\FilterProvider $filterProvider,
   \UpSage\Menu\Model\MenuFactory $menuFactory,
   array $data = []
  ) {
   parent::__construct($context, $data);
   $this->_filterProvider = $filterProvider;
   $this->_menuFactory = $menuFactory;
  }
  
  protected function _beforeToHtml() {
   parent::_beforeToHtml();
   $menuId = $this->getData('menu_id');
   $menuHash = get_class($this) . $menuId;
   if(isset(self::$_widgetUsageMap[$menuHash])) {
    return $this;
   }
   self::$_widgetUsageMap[$menuHash] = true;
   $menu = $this->getMenu();
   
   if($menu && $menu->isActive()) {
    try {
     $storeId = $this->getData('store_id') ?? $this->_storeManager->getStore()->getId();
     $this->setText(
      $this->_filterProvider->getMenuFilter()->setStoreId($storeId)->filter($menu->getContent())
     );
    } catch (NoSuchEntityException $e) {}
   }
   unset(self::$_widgetUsageMap[$menuHash]);
   return $this;
  }
  
  public function getIdentities() {
   $menu = $this->getMenu();
   if($menu) {
    return $menu->getIdentities();
   }
   return [];
  }
  
  private function getMenu(): ? gMenu {
   if($this->menu) {
    return $this->menu;
   }
   $menuId = $this->getData('menu_id');
   if($menuId) {
    try {
     $storeId = $this->_storeManager->getStore()->getId();
     $menu = $this->_menuFactory->create();
     $menu->setStoreId($storeId)->load($menuId);
     $this->menu = $menu;
     return $menu;
    } catch (NoSuchEntityException $e) {}
   }
   return null;
  }

 }

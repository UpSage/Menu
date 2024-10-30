<?php

 namespace UpSage\Menu\Block;
 
 use Magento\Framework\View\Element\AbstractBlock;
 
 class Menu extends AbstractBlock implements \Magento\Framework\DataObject\IdentityInterface {
    
  const CACHE_KEY_PREFIX = 'MENU_';
  
  protected $_filterProvider;
  
  protected $_storeManager;
  
  protected $_menuFactory;
  
  public function __construct(
   \Magento\Framework\View\Element\Context $context,
   \UpSage\Menu\Model\Template\FilterProvider $filterProvider,
   \Magento\Store\Model\StoreManagerInterface $storeManager,
   \UpSage\Menu\Model\MenuFactory $menuFactory,
   array $data = []
  ) {
   parent::__construct($context, $data);
   $this->_filterProvider = $filterProvider;
   $this->_storeManager = $storeManager;
   $this->_menuFactory = $menuFactory;
  }
  
  protected function _toHtml() {
   $menuId = $this->getMenuId();
   $html = '';
   if($menuId) {
    $storeId = $this->_storeManager->getStore()->getId();
    $menu = $this->_menuFactory->create();
    $menu->setStoreId($storeId)->load($menuId);
    if($menu->isActive()) {
     $html = $this->_filterProvider->getMenuFilter()->setStoreId($storeId)->filter($menu->getContent());
    }
   }
   return $html;
  }
  
  public function getIdentities() {
   return [\UpSage\Menu\Model\Menu::CACHE_TAG . '_' . $this->getMenuId()];
  }
  
  public function getCacheKeyInfo() {
   $cacheKeyInfo = parent::getCacheKeyInfo();
   $cacheKeyInfo[] = $this->_storeManager->getStore()->getId();
   return $cacheKeyInfo;
  }

 }

<?php

 declare(strict_types=1);
 
 namespace UpSage\Menu\Model\Template;
 
 class FilterProvider {
    
  protected $_objectManager;
  
  protected $_menuFilter;
  
  protected $_instanceList;
  
  public function __construct(
   \Magento\Framework\ObjectManagerInterface $objectManager,
   $menuFilter = \UpSage\Menu\Model\Template\Filter::class
  ) {
   $this->_objectManager = $objectManager;
   $this->_menuFilter = $menuFilter;
  }
  
  protected function _getFilterInstance($instanceName) {
   if(!isset($this->_instanceList[$instanceName])) {
    $instance = $this->_objectManager->get($instanceName);
    if(!$instance instanceof \Magento\Framework\Filter\Template) {
     throw new \Exception('Template filter ' . $instanceName . ' does not implement required interface');
    }
    $this->_instanceList[$instanceName] = $instance;
   }
   return $this->_instanceList[$instanceName];
  }
  
  public function getMenuFilter() {
   return $this->_getFilterInstance($this->_menuFilter);
  }

 }
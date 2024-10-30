<?php

 namespace UpSage\Menu\Model\ResourceModel\Menu\Relation\Store;
 
 use UpSage\Menu\Model\ResourceModel\Menu;
 use Magento\Framework\EntityManager\Operation\ExtensionInterface;
 
 class ReadHandler implements ExtensionInterface {
    
  protected $resourceMenu;
  
  public function __construct(
   Menu $resourceMenu
  ) {
   $this->resourceMenu = $resourceMenu;
  }
  
  public function execute($entity, $arguments = []) {
   if($entity->getId()) {
    $stores = $this->resourceMenu->lookupStoreIds((int)$entity->getId());
    $entity->setData('store_id', $stores);
    $entity->setData('stores', $stores);
   }
   return $entity;
  }

 }

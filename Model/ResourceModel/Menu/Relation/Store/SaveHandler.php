<?php

 namespace UpSage\Menu\Model\ResourceModel\Menu\Relation\Store;
 
 use Magento\Framework\EntityManager\Operation\ExtensionInterface;
 use UpSage\Menu\Api\Data\MenuInterface;
 use UpSage\Menu\Model\ResourceModel\Menu;
 use Magento\Framework\EntityManager\MetadataPool;
 
 class SaveHandler implements ExtensionInterface {
    
  protected $metadataPool;
  
  protected $resourceMenu;
  
  public function __construct(
   MetadataPool $metadataPool,
   Menu $resourceMenu
  ) {
   $this->metadataPool = $metadataPool;
   $this->resourceMenu = $resourceMenu;
  }
  
  public function execute($entity, $arguments = []) {
   $entityMetadata = $this->metadataPool->getMetadata(MenuInterface::class);
   $linkField = $entityMetadata->getLinkField();
   $connection = $entityMetadata->getEntityConnection();
   $oldStores = $this->resourceMenu->lookupStoreIds((int)$entity->getId());
   $newStores = (array)$entity->getStores();
   $table = $this->resourceMenu->getTable('upsage_menu_store');
   $delete = array_diff($oldStores, $newStores);
   if($delete) {
    $where = [
     $linkField . ' = ?' => (int)$entity->getData($linkField),
     'store_id IN (?)' => $delete,
    ];
    $connection->delete($table, $where);
   }
   $insert = array_diff($newStores, $oldStores);
   if($insert) {
    $data = [];
    foreach($insert as $storeId) {
     $data[] = [
      $linkField => (int)$entity->getData($linkField),
      'store_id' => (int)$storeId,
     ];
    }
    $connection->insertMultiple($table, $data);
   }
   return $entity;
  }

 }

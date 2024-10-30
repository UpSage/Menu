<?php

 namespace UpSage\Menu\Model\ResourceModel\Menu;
 
 use UpSage\Menu\Api\Data\MenuInterface;
 use \UpSage\Menu\Model\ResourceModel\AbstractCollection;
 
 class Collection extends AbstractCollection {
    
  protected $_idFieldName = 'menu_id';
  
  protected $_eventPrefix = 'upsage_menu_collection';
  
  protected $_eventObject = 'menu_collection';
  
  protected function _afterLoad() {
   $entityMetadata = $this->metadataPool->getMetadata(MenuInterface::class);
   $this->performAfterLoad('upsage_menu_store', $entityMetadata->getLinkField());
   return parent::_afterLoad();
  }
  
  protected function _construct() {
   $this->_init(\UpSage\Menu\Model\Menu::class, \UpSage\Menu\Model\ResourceModel\Menu::class);
   $this->_map['fields']['store'] = 'store_table.store_id';
   $this->_map['fields']['menu_id'] = 'main_table.menu_id';
  }
  
  public function toOptionArray() {
   return $this->_toOptionArray('menu_id', 'title');
  }
  
  public function addStoreFilter($store, $withAdmin = true) {
   $this->performAddStoreFilter($store, $withAdmin);
   return $this;
  }
  
  protected function _renderFiltersBefore() {
   $entityMetadata = $this->metadataPool->getMetadata(MenuInterface::class);
   $this->joinStoreRelationTable('upsage_menu_store', $entityMetadata->getLinkField());
  }

 }

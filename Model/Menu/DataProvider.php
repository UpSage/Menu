<?php

 namespace UpSage\Menu\Model\Menu;
 
 use UpSage\Menu\Model\ResourceModel\Menu\CollectionFactory;
 use Magento\Framework\App\Request\DataPersistorInterface;
 use Magento\Ui\DataProvider\Modifier\PoolInterface;
 
 class DataProvider extends \Magento\Ui\DataProvider\ModifierPoolDataProvider {
    
  protected $collection;
  
  protected $dataPersistor;
  
  protected $loadedData;
  
  public function __construct(
   $name,
   $primaryFieldName,
   $requestFieldName,
   CollectionFactory $menuCollectionFactory,
   DataPersistorInterface $dataPersistor,
   array $meta = [],
   array $data = [],
   PoolInterface $pool = null
  ) {
   $this->collection = $menuCollectionFactory->create();
   $this->dataPersistor = $dataPersistor;
   parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data, $pool);
  }
  
  public function getData() {
   if(isset($this->loadedData)) {
    return $this->loadedData;
   }
   $items = $this->collection->getItems();
   foreach($items as $menu) {
    $this->loadedData[$menu->getId()] = $menu->getData();
   }
   $data = $this->dataPersistor->get('menu');
   if(!empty($data)) {
    $menu = $this->collection->getNewEmptyItem();
    $menu->setData($data);
    $this->loadedData[$menu->getId()] = $menu->getData();
    $this->dataPersistor->clear('menu');
   }
   return $this->loadedData;
  }

 }
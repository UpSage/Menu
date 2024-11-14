<?php

 namespace UpSage\Menu\Model;
 
 use UpSage\Menu\Api\MenuRepositoryInterface;
 use UpSage\Menu\Api\Data;
 use UpSage\Menu\Model\ResourceModel\Menu as ResourceMenu;
 use UpSage\Menu\Model\ResourceModel\Menu\CollectionFactory as MenuCollectionFactory;
 use Magento\Framework\Api\DataObjectHelper;
 use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
 use Magento\Framework\App\ObjectManager;
 use Magento\Framework\Exception\CouldNotDeleteException;
 use Magento\Framework\Exception\CouldNotSaveException;
 use Magento\Framework\Exception\NoSuchEntityException;
 use Magento\Framework\Reflection\DataObjectProcessor;
 use Magento\Store\Model\StoreManagerInterface;
 use Magento\Framework\EntityManager\HydratorInterface;
 
 class MenuRepository implements MenuRepositoryInterface {
    
  protected $resource;
  
  protected $menuFactory;
  
  protected $menuCollectionFactory;
  
  protected $searchResultsFactory;
  
  protected $dataObjectHelper;
  
  protected $dataObjectProcessor;
  
  protected $dataMenuFactory;
  
  private $storeManager;
  
  private $collectionProcessor;
  
  private $hydrator;
  
  public function __construct(
   ResourceMenu $resource,
   MenuFactory $menuFactory,
   \UpSage\Menu\Api\Data\MenuInterfaceFactory $dataMenuFactory,
   MenuCollectionFactory $menuCollectionFactory,
   Data\MenuSearchResultsInterfaceFactory $searchResultsFactory,
   DataObjectHelper $dataObjectHelper,
   DataObjectProcessor $dataObjectProcessor,
   StoreManagerInterface $storeManager,
   CollectionProcessorInterface $collectionProcessor = null,
   ?HydratorInterface $hydrator = null
  ) {
   $this->resource = $resource;
   $this->menuFactory = $menuFactory;
   $this->menuCollectionFactory = $menuCollectionFactory;
   $this->searchResultsFactory = $searchResultsFactory;
   $this->dataObjectHelper = $dataObjectHelper;
   $this->dataMenuFactory = $dataMenuFactory;
   $this->dataObjectProcessor = $dataObjectProcessor;
   $this->storeManager = $storeManager;
   $this->collectionProcessor = $collectionProcessor ?: $this->getCollectionProcessor();
   $this->hydrator = $hydrator ?? ObjectManager::getInstance()->get(HydratorInterface::class);
  }
  
  public function save(Data\MenuInterface $menu) {
   if(empty($menu->getStoreId())) {
    $menu->setStoreId($this->storeManager->getStore()->getId());
   }
   if($menu->getId() && $menu instanceof Menu && !$menu->getOrigData()) {
    $menu = $this->hydrator->hydrate($this->getById($menu->getId()), $this->hydrator->extract($menu));
   }
   try {
    $this->resource->save($menu);
   } catch (\Exception $exception) {
    throw new CouldNotSaveException(__($exception->getMessage()));
   }
   return $menu;
  }
  
  public function getById($menuId) {
   $menu = $this->menuFactory->create();
   $this->resource->load($menu, $menuId);
   if (!$menu->getId()) {
    throw new NoSuchEntityException(__('The Menu with the "%1" ID doesn\'t exist.', $menuId));
   }
   return $menu;
  }
  
  public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria) {
   $collection = $this->menuCollectionFactory->create();
   $this->collectionProcessor->process($criteria, $collection);
   $searchResults = $this->searchResultsFactory->create();
   $searchResults->setSearchCriteria($criteria);
   $searchResults->setItems($collection->getItems());
   $searchResults->setTotalCount($collection->getSize());
   return $searchResults;
  }
  
  public function delete(Data\MenuInterface $menu) {
   try {
    $this->resource->delete($menu);
   } catch (\Exception $exception) {
    throw new CouldNotDeleteException(__($exception->getMessage()));
   }
   return true;
  }
  
  public function deleteById($menuId) {
   return $this->delete($this->getById($menuId));
  }
  
  private function getCollectionProcessor() {
   if (!$this->collectionProcessor) {
    $this->collectionProcessor = \Magento\Framework\App\ObjectManager::getInstance()->get(
     'UpSage\Menu\Model\Api\SearchCriteria\MenuCollectionProcessor'
    );
   }
   return $this->collectionProcessor;
  }

 }
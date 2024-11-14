<?php

 namespace UpSage\Menu\Controller\Adminhtml\Menu\Url;
 
 use Magento\Backend\App\Action\Context;
 use Magento\Framework\Controller\Result\JsonFactory;
 use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
 use Magento\Cms\Model\ResourceModel\Page\CollectionFactory as PageCollectionFactory;
 use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
 use Magento\Catalog\Model\ResourceModel\Category\Collection as CategoryCollection;
 use Magento\Catalog\Model\CategoryFactory;
 
 class Fetch extends \Magento\Backend\App\Action {
  protected $resultJsonFactory;
  protected $productCollectionFactory;
  protected $pageCollectionFactory;
  protected $categoryCollectionFactory;
  protected $categoryFactory;
  
  public function __construct(
   Context $context,
   JsonFactory $resultJsonFactory,
   ProductCollectionFactory $productCollectionFactory,
   PageCollectionFactory $pageCollectionFactory,
   CategoryCollectionFactory $categoryCollectionFactory,
   CategoryFactory $categoryFactory
  ) {
   parent::__construct($context);
   $this->resultJsonFactory = $resultJsonFactory;
   $this->productCollectionFactory = $productCollectionFactory;
   $this->pageCollectionFactory = $pageCollectionFactory;
   $this->categoryCollectionFactory = $categoryCollectionFactory;
   $this->categoryFactory = $categoryFactory;
  }
  
  public function execute() {
   $type = $this->getRequest()->getParam('type');
   $searchQuery = $this->getRequest()->getParam('query');
   $urls = [];
   
   switch ($type) {
    case 'cms_page':
     $pages = $this->pageCollectionFactory->create()
      ->addFieldToSelect(['identifier', 'title']);
     if($searchQuery) {
      $pages->addFieldToFilter(
       ['identifier', 'title'],
       [
        ['like' => '%' . $searchQuery . '%'],
        ['like' => '%' . $searchQuery . '%']
       ]
      );
     }
     foreach ($pages as $page) {
      $urls[] = [
       'identifier' => $page->getIdentifier(),
       'title' => $page->getTitle()
      ];
     }
     break;
    case 'product':
     $products = $this->productCollectionFactory->create()
      ->addAttributeToSelect(['name', 'sku', 'url_key']);
     if($searchQuery) {
      $products->addAttributeToFilter(
       [
        ['attribute' => 'name', 'like' => '%' . $searchQuery . '%'],
        ['attribute' => 'sku', 'like' => '%' . $searchQuery . '%']
       ]
      );
     }
     foreach ($products as $product) {
      $urls[] = [
       'name' => $product->getName(),
       'sku' => $product->getSku(),
       'url_key' => $product->getUrlKey()
      ];
     }
     break;
    case 'category':
     $categories = $this->categoryCollectionFactory->create()
      ->addAttributeToSelect(['name', 'url_key', 'path', 'level'])
      ->addIsActiveFilter()
      ->setOrder('level', 'ASC')
      ->setOrder('position', 'ASC');
     if($searchQuery) {
      $categories->addAttributeToFilter('name', ['like' => '%' . $searchQuery . '%']);
     }
     if($categories->getSize() > 0) {
      $urls = $this->buildCategoryHierarchy($categories);
     }
     break;
   }
   
   $result = $this->resultJsonFactory->create();
   return $result->setData(['urls' => $urls]);
  }
  
  protected function buildCategoryHierarchy(CategoryCollection $categories) {
   $categoryTree = [];
   foreach($categories as $category) {
    $categoryData = [
     'id' => $category->getId(),
     'name' => $category->getName(),
     'url_key' => $category->getUrlKey() ?: 'no-url-key',
     'level' => $category->getLevel(),
     'product_count' => $this->getCategoryProductCount($category->getId()),
     'children' => []
    ];
    $path = explode('/', $category->getPath());
    $parent = &$categoryTree;
    foreach(array_slice($path, 1, -1) as $parentId) {
     if(!isset($parent[$parentId])) {
      $parent[$parentId] = ['children' => []];
     }
     $parent = &$parent[$parentId]['children'];
    }
    $parent[$category->getId()] = $categoryData;
   }
   return $this->formatCategoryTree($categoryTree);
  }
  
  protected function formatCategoryTree(array $categoryTree) {
   $result = [];
   foreach($categoryTree as $categoryData) {
    if(isset($categoryData['id'])) {
     $formattedCategory = [
      'id' => $categoryData['id'],
      'name' => $categoryData['name'],
      'url_key' => $categoryData['url_key'],
      'level' => $categoryData['level'],
      'product_count' => $categoryData['product_count']
     ];
     if(!empty($categoryData['children'])) {
      $formattedCategory['children'] = $this->formatCategoryTree($categoryData['children']);
     }
     $result[] = $formattedCategory;
    } else {
     $result = array_merge($result, $this->formatCategoryTree($categoryData['children']));
    }
   }
   return $result;
  }
  
  protected function getCategoryProductCount($categoryId) {
   $category = $this->categoryFactory->create()->load($categoryId);
   return $category->getProductCollection()->getSize();
  }
  
  protected function _isAllowed() {
   return $this->_authorization->isAllowed('UpSage_Menu::fetch');
  }
 
 }
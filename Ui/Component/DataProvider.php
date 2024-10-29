<?php

 namespace UpSage\Menu\Ui\Component;
 
 use Magento\Framework\Api\Filter;
 use Magento\Framework\Api\FilterBuilder;
 use Magento\Framework\Api\Search\SearchCriteriaBuilder;
 use Magento\Framework\App\ObjectManager;
 use Magento\Framework\App\RequestInterface;
 use Magento\Framework\AuthorizationInterface;
 use Magento\Framework\View\Element\UiComponent\DataProvider\Reporting;
 
 class DataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider {
    
  private $authorization;

  private $additionalFilterPool;

  public function __construct(
   $name,
   $primaryFieldName,
   $requestFieldName,
   Reporting $reporting,
   SearchCriteriaBuilder $searchCriteriaBuilder,
   RequestInterface $request,
   FilterBuilder $filterBuilder,
   array $meta = [],
   array $data = [],
   array $additionalFilterPool = []
  ) {
   parent::__construct(
    $name,
    $primaryFieldName,
    $requestFieldName,
    $reporting,
    $searchCriteriaBuilder,
    $request,
    $filterBuilder,
    $meta,
    $data
   );
   $this->additionalFilterPool = $additionalFilterPool;
  }
  
  private function getAuthorizationInstance() {
   if($this->authorization === null) {
    $this->authorization = ObjectManager::getInstance()->get(AuthorizationInterface::class);
   }
   return $this->authorization;
  }
  
  public function addFilter(Filter $filter) {
   if(!empty($this->additionalFilterPool[$filter->getField()])) {
    $this->additionalFilterPool[$filter->getField()]->addFilter($this->searchCriteriaBuilder, $filter);
   } else {
    parent::addFilter($filter);
   }
  }

}

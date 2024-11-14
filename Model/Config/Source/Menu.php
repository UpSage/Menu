<?php

 declare(strict_types=1);
 
 namespace UpSage\Menu\Model\Config\Source;
 
 use UpSage\Menu\Model\ResourceModel\Menu\CollectionFactory;
 use Magento\Framework\Data\OptionSourceInterface;
 
 class Menu implements OptionSourceInterface {
    
  private $options;
  
  private $collectionFactory;
  
  public function __construct(
   CollectionFactory $collectionFactory
  ) {
   $this->collectionFactory = $collectionFactory;
  }
  
  public function toOptionArray() {
   if(!$this->options) {
    $this->options = $this->collectionFactory->create()->toOptionIdArray();
   }
   return $this->options;
  }

 }
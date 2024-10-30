<?php

 namespace UpSage\Menu\Model\Menu\Source;
 
 use Magento\Framework\Data\OptionSourceInterface;
 
 class IsActive implements OptionSourceInterface {
    
  protected $menu;
  
  public function __construct(\Upsage\Menu\Model\Menu $menu) {
   $this->menu = $menu;
  }
  
  public function toOptionArray() {
   $availableOptions = $this->menu->getAvailableStatuses();
   $options = [];
   foreach($availableOptions as $key => $value) {
    $options[] = [
     'label' => $value,
     'value' => $key,
    ];
   }
   return $options;
  }

 }

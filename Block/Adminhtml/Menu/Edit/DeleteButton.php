<?php

 namespace UpSage\Menu\Block\Adminhtml\Menu\Edit;
 
 use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
 
 class DeleteButton extends GenericButton implements ButtonProviderInterface {
    
  public function getButtonData() {
   $data = [];
   if($this->getMenuId()) {
    $data = [
     'label' => __('Delete Menu'),
     'class' => 'delete',
     'on_click' => 'deleteConfirm(\'' . __('Are you sure you want to do this?') . '\', \'' . $this->getDeleteUrl() . '\', {"data": {}})',
     'sort_order' => 20,
    ];
   }
   return $data;
  }
  
  public function getDeleteUrl() {
   return $this->getUrl('*/*/delete', ['menu_id' => $this->getMenuId()]);
  }

 }
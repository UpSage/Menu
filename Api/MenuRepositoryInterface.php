<?php

 namespace UpSage\Menu\Api;
 
 interface MenuRepositoryInterface {
    
  public function save(Data\MenuInterface $menu);
  
  public function getById($menuId);
  
  public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
  
  public function delete(Data\MenuInterface $menu);
  
  public function deleteById($menuId);

 }
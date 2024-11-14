<?php

 namespace UpSage\Menu\Api\Data;
 
 use Magento\Framework\Api\SearchResultsInterface;
 
 interface MenuSearchResultsInterface extends SearchResultsInterface {
  public function getItems();
  public function setItems(array $items);
 }
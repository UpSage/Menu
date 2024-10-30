<?php

 namespace UpSage\Menu\Api;
 
 interface GetMenuByIdentifierInterface {
  public function execute(string $identifier, int $storeId) : \UpSage\Menu\Api\Data\MenuInterface;
 }

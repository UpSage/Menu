<?php

 declare(strict_types=1);
 
 namespace UpSage\Menu\Model;
 
 use UpSage\Menu\Api\Data\MenuSearchResultsInterface;
 use Magento\Framework\Api\SearchResults;
 
 class MenuSearchResults extends SearchResults implements MenuSearchResultsInterface {}
<?php

namespace UpSage\Menu\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;


interface MenuSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get menu list.
     *
     * @return \UpSage\Menu\Api\Data\MenuInterface[]
     */
    public function getItems();

    /**
     * Set menu list.
     *
     * @param \UpSage\Menu\Api\Data\MenuInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

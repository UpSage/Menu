<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace UpSage\Menu\Api;

/**
 * Menu repository CRUD interface.
 * @api
 * @since 100.0.2
 */
interface MenuRepositoryInterface
{
    /**
     * Save menu.
     *
     * @param \UpSage\Menu\Api\Data\MenuInterface $menu
     * @return \UpSage\Menu\Api\Data\MenuInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(Data\MenuInterface $menu);

    /**
     * Retrieve menu by ID.
     *
     * @param string $menuId
     * @return \UpSage\Menu\Api\Data\MenuInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($menuId);

    /**
     * Retrieve menus matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \UpSage\Menu\Api\Data\MenuSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete menu.
     *
     * @param \UpSage\Menu\Api\Data\MenuInterface $menu
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(Data\MenuInterface $menu);

    /**
     * Delete menu by ID.
     *
     * @param string $menuId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($menuId);
}

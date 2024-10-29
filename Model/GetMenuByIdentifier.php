<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace UpSage\Menu\Model;

use UpSage\Menu\Api\GetMenuByIdentifierInterface;
use UpSage\Menu\Api\Data\MenuInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class GetMenuByIdentifier
 */
class GetMenuByIdentifier implements GetMenuByIdentifierInterface
{
    /**
     * @var \UpSage\Menu\Model\MenuFactory
     */
    private $menuFactory;

    /**
     * @var ResourceModel\Menu
     */
    private $menuResource;

    /**
     * @param MenuFactory $menuFactory
     * @param ResourceModel\Menu $menuResource
     */
    public function __construct(
        \UpSage\Menu\Model\MenuFactory $menuFactory,
        \UpSage\Menu\Model\ResourceModel\Menu $menuResource
    ) {
        $this->menuFactory = $menuFactory;
        $this->menuResource = $menuResource;
    }

    /**
     * @inheritdoc
     */
    public function execute(string $identifier, int $storeId): MenuInterface
    {
        $menu = $this->menuFactory->create();
        $menu->setStoreId($storeId);
        $this->menuResource->load($menu, $identifier, MenuInterface::IDENTIFIER);

        if (!$menu->getId()) {
            throw new NoSuchEntityException(__('The menu with the "%1" ID doesn\'t exist.', $identifier));
        }

        return $menu;
    }
}

<?php
namespace UpSage\Menu\Model\ResourceModel\Menu\Relation\Store;

use UpSage\Menu\Model\ResourceModel\Menu;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

/**
 * Class ReadHandler
 */
class ReadHandler implements ExtensionInterface
{
    /**
     * @var Menu
     */
    protected $resourceMenu;

    /**
     * @param Menu $resourceMenu
     */
    public function __construct(
        Menu $resourceMenu
    ) {
        $this->resourceMenu = $resourceMenu;
    }

    /**
     * @param object $entity
     * @param array $arguments
     * @return object
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        if ($entity->getId()) {
            $stores = $this->resourceMenu->lookupStoreIds((int)$entity->getId());
            $entity->setData('store_id', $stores);
            $entity->setData('stores', $stores);
        }
        return $entity;
    }
}

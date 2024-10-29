<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace UpSage\Menu\Model\ResourceModel;

use UpSage\Menu\Api\Data\MenuInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Menu resource model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Menu extends AbstractDb
{
    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param EntityManager $entityManager
     * @param MetadataPool $metadataPool
     * @param string $connectionName
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        EntityManager $entityManager,
        MetadataPool $metadataPool,
        $connectionName = null
    ) {
        $this->_storeManager = $storeManager;
        $this->entityManager = $entityManager;
        $this->metadataPool = $metadataPool;
        parent::__construct($context, $connectionName);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('upsage_menu', 'menu_id');
    }

    /**
     * @inheritDoc
     */
    public function getConnection()
    {
        return $this->metadataPool->getMetadata(MenuInterface::class)->getEntityConnection();
    }

    /**
     * Perform operations before object save
     *
     * @param AbstractModel $object
     * @return $this
     * @throws LocalizedException
     */
    protected function _beforeSave(AbstractModel $object)
    {
        if (!$this->getIsUniqueMenuToStores($object)) {
            throw new LocalizedException(
                __('A menu identifier with the same properties already exists in the selected store.')
            );
        }
        return $this;
    }

    /**
     * Get menu id.
     *
     * @param AbstractModel $object
     * @param mixed $value
     * @param string $field
     * @return bool|int|string
     * @throws LocalizedException
     * @throws \Exception
     */
    private function getMenuId(AbstractModel $object, $value, $field = null)
    {
        $entityMetadata = $this->metadataPool->getMetadata(MenuInterface::class);
        if (!is_numeric($value) && $field === null) {
            $field = 'identifier';
        } elseif (!$field) {
            $field = $entityMetadata->getIdentifierField();
        }
        $entityId = $value;
        if ($field != $entityMetadata->getIdentifierField() || $object->getStoreId()) {
            $select = $this->_getLoadSelect($field, $value, $object);
            $select->reset(Select::COLUMNS)
                ->columns($this->getMainTable() . '.' . $entityMetadata->getIdentifierField())
                ->limit(1);
            $result = $this->getConnection()->fetchCol($select);
            $entityId = count($result) ? $result[0] : false;
        }
        return $entityId;
    }

    /**
     * Load an object
     *
     * @param \UpSage\Menu\Model\Menu|AbstractModel $object
     * @param mixed $value
     * @param string $field field to load by (defaults to model id)
     * @return $this
     */
    public function load(AbstractModel $object, $value, $field = null)
    {
        $menuId = $this->getMenuId($object, $value, $field);
        if ($menuId) {
            $this->entityManager->load($object, $menuId);
        }
        return $this;
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param \UpSage\Menu\Model\Menu|AbstractModel $object
     * @return Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $entityMetadata = $this->metadataPool->getMetadata(MenuInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $select = parent::_getLoadSelect($field, $value, $object);

        if ($object->getStoreId()) {
            $stores = [(int)$object->getStoreId(), Store::DEFAULT_STORE_ID];

            $select->join(
                ['ums' => $this->getTable('upsage_menu_store')],
                $this->getMainTable() . '.' . $linkField . ' = ums.' . $linkField,
                ['store_id']
            )
                ->where('is_active = ?', 1)
                ->where('ums.store_id in (?)', $stores)
                ->order('store_id DESC')
                ->limit(1);
        }

        return $select;
    }

    /**
     * Check for unique identifier of menu to selected store(s).
     *
     * @param AbstractModel $object
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsUniqueMenuToStores(AbstractModel $object)
    {
        $entityMetadata = $this->metadataPool->getMetadata(MenuInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $stores = $this->_storeManager->isSingleStoreMode()
            ? [Store::DEFAULT_STORE_ID]
            : (array)$object->getData('store_id');

        $select = $this->getConnection()->select()
            ->from(['um' => $this->getMainTable()])
            ->join(
                ['ums' => $this->getTable('upsage_menu_store')],
                'um.' . $linkField . ' = ums.' . $linkField,
                []
            )
            ->where('um.identifier = ?', $object->getData('identifier'))
            ->where('ums.store_id IN (?)', $stores);

        if ($object->getId()) {
            $select->where('um.' . $entityMetadata->getIdentifierField() . ' <> ?', $object->getId());
        }

        return !$this->getConnection()->fetchRow($select);
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $id
     * @return array
     */
    public function lookupStoreIds($id)
    {
        $connection = $this->getConnection();

        $entityMetadata = $this->metadataPool->getMetadata(MenuInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $select = $connection->select()
            ->from(['ums' => $this->getTable('upsage_menu_store')], 'store_id')
            ->join(
                ['um' => $this->getMainTable()],
                'ums.' . $linkField . ' = um.' . $linkField,
                []
            )
            ->where('um.' . $entityMetadata->getIdentifierField() . ' = :menu_id');

        return $connection->fetchCol($select, ['menu_id' => (int)$id]);
    }

    /**
     * Save an object.
     *
     * @param AbstractModel $object
     * @return $this
     * @throws \Exception
     */
    public function save(AbstractModel $object)
    {
        $this->entityManager->save($object);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function delete(AbstractModel $object)
    {
        $this->entityManager->delete($object);
        return $this;
    }
}

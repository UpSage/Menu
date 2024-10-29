<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace UpSage\Menu\Model\ResourceModel\Menu;

use UpSage\Menu\Api\Data\MenuInterface;
use \UpSage\Menu\Model\ResourceModel\AbstractCollection;

/**
 * Menu Collection
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'menu_id';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'upsage_menu_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'menu_collection';

    /**
     * Perform operations after collection load
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        $entityMetadata = $this->metadataPool->getMetadata(MenuInterface::class);

        $this->performAfterLoad('upsage_menu_store', $entityMetadata->getLinkField());

        return parent::_afterLoad();
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\UpSage\Menu\Model\Menu::class, \UpSage\Menu\Model\ResourceModel\Menu::class);
        $this->_map['fields']['store'] = 'store_table.store_id';
        $this->_map['fields']['menu_id'] = 'main_table.menu_id';
    }

    /**
     * Returns pairs menu_id - title
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('menu_id', 'title');
    }

    /**
     * Add filter by store
     *
     * @param int|array|\Magento\Store\Model\Store $store
     * @param bool $withAdmin
     * @return $this
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        $this->performAddStoreFilter($store, $withAdmin);

        return $this;
    }

    /**
     * Join store relation table if there is store filter
     *
     * @return void
     */
    protected function _renderFiltersBefore()
    {
        $entityMetadata = $this->metadataPool->getMetadata(MenuInterface::class);
        $this->joinStoreRelationTable('upsage_menu_store', $entityMetadata->getLinkField());
    }
}

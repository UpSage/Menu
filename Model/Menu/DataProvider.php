<?php
/**
 * Copyright © UpSage, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace UpSage\Menu\Model\Menu;

use UpSage\Menu\Model\ResourceModel\Menu\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;

/**
 * Class DataProvider
 */
class DataProvider extends \Magento\Ui\DataProvider\ModifierPoolDataProvider
{
    /**
     * @var \UpSage\Menu\Model\ResourceModel\Menu\Collection
     */
    protected $collection;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * Constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $menuCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     * @param PoolInterface|null $pool
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $menuCollectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = [],
        PoolInterface $pool = null
    ) {
        $this->collection = $menuCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data, $pool);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        /** @var \UpSage\Menu\Model\Menu $menu */
        foreach ($items as $menu) {
            $this->loadedData[$menu->getId()] = $menu->getData();
        }

        $data = $this->dataPersistor->get('menu');
        if (!empty($data)) {
            $menu = $this->collection->getNewEmptyItem();
            $menu->setData($data);
            $this->loadedData[$menu->getId()] = $menu->getData();
            $this->dataPersistor->clear('menu');
        }

        return $this->loadedData;
    }
}

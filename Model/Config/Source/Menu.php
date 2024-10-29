<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace UpSage\Menu\Model\Config\Source;

use UpSage\Menu\Model\ResourceModel\Menu\CollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Menu
 */
class Menu implements OptionSourceInterface
{
    /**
     * @var array
     */
    private $options;

    /**
     * @var \UpSage\Menu\Model\ResourceModel\Menu\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @param \UpSage\Menu\Model\ResourceModel\Menu\CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $this->options = $this->collectionFactory->create()->toOptionIdArray();
        }

        return $this->options;
    }
}

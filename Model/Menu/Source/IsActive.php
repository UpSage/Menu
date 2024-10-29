<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace UpSage\Menu\Model\Menu\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class IsActive
 */
class IsActive implements OptionSourceInterface
{
    /**
     * @var \Upsage\Menu\Model\Menu
     */
    protected $menu;

    /**
     * Constructor
     *
     * @param \Upsage\Menu\Model\Menu $menu
     */
    public function __construct(\Upsage\Menu\Model\Menu $menu)
    {
        $this->menu = $menu;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $availableOptions = $this->menu->getAvailableStatuses();
        $options = [];
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }
}

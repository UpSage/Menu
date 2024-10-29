<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace UpSage\Menu\Block\Widget;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use UpSage\Menu\Model\Menu as UpSageMenu;
use Magento\Widget\Block\BlockInterface;

/**
 * Cms Static Block Widget
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Menu extends \Magento\Framework\View\Element\Template implements BlockInterface, IdentityInterface
{
    /**
     * @var \UpSage\Menu\Model\Template\FilterProvider
     */
    protected $_filterProvider;

    /**
     * Storage for used widgets
     *
     * @var array
     */
    protected static $_widgetUsageMap = [];

    /**
     * Block factory
     *
     * @var \UpSage\Menu\Model\MenuFactory
     */
    protected $_menuFactory;

    /**
     * @var UpSageMenu
     */
    private $menu;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \UpSage\Menu\Model\Template\FilterProvider $filterProvider
     * @param \UpSage\Menu\Model\MenuFactory $menuFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \UpSage\Menu\Model\Template\FilterProvider $filterProvider,
        \UpSage\Menu\Model\MenuFactory $menuFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_filterProvider = $filterProvider;
        $this->_menuFactory = $menuFactory;
    }

    /**
     * Prepare block text and determine whether block output enabled or not.
     *
     * Prevent blocks recursion if needed.
     *
     * @return $this
     */
    protected function _beforeToHtml()
    {
        parent::_beforeToHtml();
        $menuId = $this->getData('menu_id');
        $menuHash = get_class($this) . $menuId;

        if (isset(self::$_widgetUsageMap[$menuHash])) {
            return $this;
        }
        self::$_widgetUsageMap[$menuHash] = true;

        $menu = $this->getMenu();

        if ($menu && $menu->isActive()) {
            try {
                $storeId = $this->getData('store_id') ?? $this->_storeManager->getStore()->getId();
                $this->setText(
                    $this->_filterProvider->getMenuFilter()->setStoreId($storeId)->filter($menu->getContent())
                );
            } catch (NoSuchEntityException $e) {
            }
        }
        unset(self::$_widgetUsageMap[$menuHash]);
        return $this;
    }

    /**
     * Get identities of the Cms Block
     *
     * @return array
     */
    public function getIdentities()
    {
        $menu = $this->getMenu();

        if ($menu) {
            return $menu->getIdentities();
        }

        return [];
    }

    /**
     * Get block
     *
     * @return UpSageMenu|null
     */
    private function getMenu(): ?UpSageMenu
    {
        if ($this->menu) {
            return $this->menu;
        }

        $menuId = $this->getData('menu_id');

        if ($menuId) {
            try {
                $storeId = $this->_storeManager->getStore()->getId();
                /** @var \UpSage\Menu\Model\Menu $menu */
                $menu = $this->_menuFactory->create();
                $menu->setStoreId($storeId)->load($menuId);
                $this->menu = $menu;

                return $menu;
            } catch (NoSuchEntityException $e) {
            }
        }

        return null;
    }
}

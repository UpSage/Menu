<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace UpSage\Menu\Block;

use Magento\Framework\View\Element\AbstractBlock;

/**
 * Menu content block
 */
class Menu extends AbstractBlock implements \Magento\Framework\DataObject\IdentityInterface
{
    /**
     * Prefix for cache key of Menu
     */
    const CACHE_KEY_PREFIX = 'MENU_';

    /**
     * @var \UpSage\Menu\Model\Template\FilterProvider
     */
    protected $_filterProvider;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Menu factory
     *
     * @var \UpSage\Menu\Model\MenuFactory
     */
    protected $_menuFactory;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \UpSage\Menu\Model\Template\FilterProvider $filterProvider
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \UpSage\Menu\Model\MenuFactory $menuFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \UpSage\Menu\Model\Template\FilterProvider $filterProvider,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \UpSage\Menu\Model\MenuFactory $menuFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_filterProvider = $filterProvider;
        $this->_storeManager = $storeManager;
        $this->_menuFactory = $menuFactory;
    }

    /**
     * Prepare Content HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        $menuId = $this->getMenuId();
        $html = '';
        if ($menuId) {
            $storeId = $this->_storeManager->getStore()->getId();
            /** @var \UpSage\Menu\Model\Menu $menu */
            $menu = $this->_menuFactory->create();
            $menu->setStoreId($storeId)->load($menuId);
            if ($menu->isActive()) {
                $html = $this->_filterProvider->getMenuFilter()->setStoreId($storeId)->filter($menu->getContent());
            }
        }
        return $html;
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return [\UpSage\Menu\Model\Menu::CACHE_TAG . '_' . $this->getMenuId()];
    }

    /**
     * @inheritdoc
     */
    public function getCacheKeyInfo()
    {
        $cacheKeyInfo = parent::getCacheKeyInfo();
        $cacheKeyInfo[] = $this->_storeManager->getStore()->getId();
        return $cacheKeyInfo;
    }
}

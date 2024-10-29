<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace UpSage\Menu\Block;

use UpSage\Menu\Api\Data\MenuInterface;
use UpSage\Menu\Api\GetMenuByIdentifierInterface;
use UpSage\Menu\Model\Menu as MenuModel;
use UpSage\Menu\Model\Template\FilterProvider;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Context;
use Magento\Store\Model\StoreManagerInterface;

/**
 * This class is a replacement for \Magento\Cms\Block\Block, that accepts only `string` identifier of Menu
 */
class MenuByIdentifier extends AbstractBlock implements IdentityInterface
{
    public const CACHE_KEY_PREFIX = 'MENU';

    /**
     * @var GetMenuByIdentifierInterface
     */
    private $menuByIdentifier;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var FilterProvider
     */
    private $filterProvider;

    /**
     * @var MenuInterface
     */
    private $menu;

    /**
     * @param GetMenuByIdentifierInterface $menuByIdentifier
     * @param StoreManagerInterface $storeManager
     * @param FilterProvider $filterProvider
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        GetMenuByIdentifierInterface $menuByIdentifier,
        StoreManagerInterface $storeManager,
        FilterProvider $filterProvider,
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->menuByIdentifier = $menuByIdentifier;
        $this->storeManager = $storeManager;
        $this->filterProvider = $filterProvider;
    }

    /**
     * @inheritDoc
     */
    protected function _toHtml(): string
    {
        try {
            return $this->filterOutput(
                $this->getMenu()->getContent()
            );
        } catch (NoSuchEntityException $e) {
            return '';
        }
    }

    /**
     * Returns the value of `identifier` injected in `<block>` definition
     *
     * @return string|null
     */
    private function getIdentifier(): ?string
    {
        return $this->getData('identifier') ?: null;
    }

    /**
     * Filters the Content
     *
     * @param string $content
     * @return string
     * @throws NoSuchEntityException
     */
    private function filterOutput(string $content): string
    {
        return $this->filterProvider->getMenuFilter()
            ->setStoreId($this->getCurrentStoreId())
            ->filter($content);
    }

    /**
     * Loads the Menu by `identifier` provided as an argument
     *
     * @return MenuInterface|MenuModel
     * @throws \InvalidArgumentException
     * @throws NoSuchEntityException
     */
    private function getMenu(): MenuInterface
    {
        if (!$this->getIdentifier()) {
            throw new \InvalidArgumentException('Expected value of `identifier` was not provided');
        }

        if (null === $this->menu) {
            $this->menu = $this->menuByIdentifier->execute(
                (string)$this->getIdentifier(),
                $this->getCurrentStoreId()
            );

            if (!$this->menu->isActive()) {
                throw new NoSuchEntityException(
                    __('The menu with identifier "%identifier" is not enabled.', $this->getIdentifier())
                );
            }
        }

        return $this->menu;
    }

    /**
     * Returns the current Store ID
     *
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getCurrentStoreId(): int
    {
        return (int)$this->storeManager->getStore()->getId();
    }

    /**
     * Returns array of Menu Identifiers used to determine Cache Tags
     *
     * This implementation supports different menu caching having the same identifier,
     * resolving the bug introduced in scope of \Magento\Cms\Block\Block
     *
     * @return string[]
     */
    public function getIdentities(): array
    {
        if (!$this->getIdentifier()) {
            return [];
        }

        $identities = [
            self::CACHE_KEY_PREFIX . '_' . $this->getIdentifier(),
            self::CACHE_KEY_PREFIX . '_' . $this->getIdentifier() . '_' . $this->getCurrentStoreId()
        ];

        try {
            $menu = $this->getMenu();
            if ($menu instanceof IdentityInterface) {
                $identities = array_merge($identities, $menu->getIdentities());
            }
            // phpcs:ignore Magento2.CodeAnalysis.EmptyBlock.DetectedCatch
        } catch (NoSuchEntityException $e) {
        }

        return $identities;
    }
}

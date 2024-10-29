<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace UpSage\Menu\Block\Adminhtml\Menu\Edit;

use Magento\Backend\Block\Widget\Context;
use UpSage\Menu\Api\MenuRepositoryInterface; // Change this to your corresponding repository interface
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class GenericButton
 */
class GenericButton
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var MenuRepositoryInterface
     */
    protected $menuRepository;

    /**
     * @param Context $context
     * @param MenuRepositoryInterface $menuRepository
     */
    public function __construct(
        Context $context,
        MenuRepositoryInterface $menuRepository
    ) {
        $this->context = $context;
        $this->menuRepository = $menuRepository; // Update to use MenuRepositoryInterface
    }

    /**
     * Return Menu block ID
     *
     * @return int|null
     */
    public function getMenuId()
    {
        try {
            return $this->menuRepository->getById(
                $this->context->getRequest()->getParam('menu_id') // Make sure 'menu_id' is appropriate for your menu
            )->getId();
        } catch (NoSuchEntityException $e) {
            // Handle exception as needed
        }
        return null;
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}

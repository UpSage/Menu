<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace UpSage\Menu\Controller\Adminhtml\Menu;

use Magento\Backend\App\Action\Context;
use UpSage\Menu\Api\MenuRepositoryInterface as MenuRepository;
use Magento\Framework\Controller\Result\JsonFactory;
use UpSage\Menu\Api\Data\MenuInterface;

class InlineEdit extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'UpSage_Menu::menu';

    /**
     * @var \UpSage\Menu\Api\MenuRepositoryInterface
     */
    protected $menuRepository;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $jsonFactory;

    /**
     * @param Context $context
     * @param MenuRepository $menuRepository
     * @param JsonFactory $jsonFactory
     */
    public function __construct(
        Context $context,
        MenuRepository $menuRepository,
        JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
        $this->menuRepository = $menuRepository;
        $this->jsonFactory = $jsonFactory;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        if ($this->getRequest()->getParam('isAjax')) {
            $postItems = $this->getRequest()->getParam('items', []);
            if (!count($postItems)) {
                $messages[] = __('Please correct the data sent.');
                $error = true;
            } else {
                foreach (array_keys($postItems) as $menuId) {
                    /** @var \UpSage\Menu\Model\Menu $menu */
                    $menu = $this->menuRepository->getById($menuId);
                    try {
                        $menu->setData(array_merge($menu->getData(), $postItems[$menuId]));
                        $this->menuRepository->save($menu);
                    } catch (\Exception $e) {
                        $messages[] = $this->getErrorWithMenuId(
                            $menu,
                            __($e->getMessage())
                        );
                        $error = true;
                    }
                }
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    /**
     * Add block title to error message
     *
     * @param MenuInterface $menu
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithMenuId(MenuInterface $menu, $errorText)
    {
        return '[Menu ID: ' . $menu->getId() . '] ' . $errorText;
    }
}

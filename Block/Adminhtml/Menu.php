<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace UpSage\Menu\Block\Adminhtml;

/**
 * Adminhtml menu content block
 */
class Menu extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'UpSage_Menu';
        $this->_controller = 'adminhtml_menu';
        $this->_headerText = __('Menus');
        $this->_addButtonLabel = __('Add New Menu');
        parent::_construct();
    }
}

<?php

 namespace UpSage\Menu\Block\Adminhtml;
 
 class Menu extends \Magento\Backend\Block\Widget\Grid\Container {
    
  protected function _construct() {
   $this->_blockGroup = 'UpSage_Menu';
   $this->_controller = 'adminhtml_menu';
   $this->_headerText = __('Menus');
   $this->_addButtonLabel = __('Add New Menu');
   parent::_construct();
  }

 }

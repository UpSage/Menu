<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace UpSage\Menu\Model\Template;

/**
 * Class Menu Template Filter Provider
 *
 * @api
 */
class FilterProvider
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var string
     */
    protected $_menuFilter;

    /**
     * @var array
     */
    protected $_instanceList;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param string $menuFilter
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        $menuFilter = \UpSage\Menu\Model\Template\Filter::class
    ) {
        $this->_objectManager = $objectManager;
        $this->_menuFilter = $menuFilter;
    }

    /**
     * @param string $instanceName
     * @return \Magento\Framework\Filter\Template
     * @throws \Exception
     */
    protected function _getFilterInstance($instanceName)
    {
        if (!isset($this->_instanceList[$instanceName])) {
            $instance = $this->_objectManager->get($instanceName);

            if (!$instance instanceof \Magento\Framework\Filter\Template) {
                throw new \Exception('Template filter ' . $instanceName . ' does not implement required interface');
            }
            $this->_instanceList[$instanceName] = $instance;
        }

        return $this->_instanceList[$instanceName];
    }

    /**
     * @return \Magento\Framework\Filter\Template
     */
    public function getMenuFilter()
    {
        return $this->_getFilterInstance($this->_menuFilter);
    }

}

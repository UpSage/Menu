<?php

 namespace UpSage\Menu\Block\Adminhtml\Menu\Widget;
 
 class Chooser extends \Magento\Backend\Block\Widget\Grid\Extended {
    
  protected $_menuFactory;
  
  protected $_collectionFactory;
  
  public function __construct(
   \Magento\Backend\Block\Template\Context $context,
   \Magento\Backend\Helper\Data $backendHelper,
   \UpSage\Menu\Model\MenuFactory $menuFactory,
   \UpSage\Menu\Model\ResourceModel\Menu\CollectionFactory $collectionFactory,
   array $data = []
  ) {
   $this->_menuFactory = $menuFactory;
   $this->_collectionFactory = $collectionFactory;
   parent::__construct($context, $backendHelper, $data);
  }
  
  protected function _construct() {
   parent::_construct();
   $this->setDefaultSort('menu_identifier');
   $this->setDefaultDir('ASC');
   $this->setUseAjax(true);
   $this->setDefaultFilter(['chooser_is_active' => '1']);
  }
  
  public function prepareElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element) {
   $uniqId = $this->mathRandom->getUniqueHash($element->getId());
   $sourceUrl = $this->getUrl('upsage/menu_widget/chooser', ['uniq_id' => $uniqId]);
   $chooser = $this->getLayout()->createBlock(
    \Magento\Widget\Block\Adminhtml\Widget\Chooser::class
   )->setElement(
    $element
   )->setConfig(
    $this->getConfig()
   )->setFieldsetId(
    $this->getFieldsetId()
   )->setSourceUrl(
    $sourceUrl
   )->setUniqId(
    $uniqId
   );
   
   if($element->getValue()) {
    $menu = $this->_menuFactory->create()->load($element->getValue());
    if($menu->getId()) {
     $chooser->setLabel($this->escapeHtml($menu->getTitle()));
    }
   }
   
   $element->setData('after_element_html', $chooser->toHtml());
   return $element;
  }
  
  public function getRowClickCallback() {
   $chooserJsObject = $this->getId();
   $js = '
    function (grid, event) {
     var trElement = Event.findElement(event, "tr");
     var menuId = trElement.down("td").innerHTML.replace(/^\s+|\s+$/g,"");
     var menuTitle = trElement.down("td").next().innerHTML;' .
     $chooserJsObject . '.setElementValue(menuId); ' .
     $chooserJsObject . '.setElementLabel(menuTitle); ' .
     $chooserJsObject . '.close();
    }
   ';
   return $js;
  }
  
  protected function _prepareCollection() {
   $this->setCollection($this->_collectionFactory->create());
   return parent::_prepareCollection();
  }
  
  protected function _prepareColumns() {
    
   $this->addColumn(
    'chooser_id',
    ['header' => __('ID'), 'align' => 'right', 'index' => 'menu_id', 'width' => 50]
   );
   
   $this->addColumn('chooser_title', ['header' => __('Title'), 'align' => 'left', 'index' => 'title']);
   
   $this->addColumn(
    'chooser_identifier',
    ['header' => __('Identifier'), 'align' => 'left', 'index' => 'identifier']
   );
   
   $this->addColumn(
    'chooser_is_active',
    [
     'header' => __('Status'),
     'index' => 'is_active',
     'type' => 'options',
     'options' => [0 => __('Disabled'), 1 => __('Enabled')]
    ]
   );
   
   return parent::_prepareColumns();

  }
  
  public function getGridUrl() {
   return $this->getUrl('upsage/menu_widget/chooser', ['_current' => true]);
  }

 }

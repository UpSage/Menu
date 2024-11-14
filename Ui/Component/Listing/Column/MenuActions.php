<?php

 namespace UpSage\Menu\Ui\Component\Listing\Column;
 
 use Magento\Framework\UrlInterface;
 use Magento\Framework\View\Element\UiComponent\ContextInterface;
 use Magento\Framework\View\Element\UiComponentFactory;
 use Magento\Ui\Component\Listing\Columns\Column;
 use Magento\Framework\App\ObjectManager;
 use Magento\Framework\Escaper;
 
 class MenuActions extends Column {
    
  public const URL_PATH_EDIT = 'upsage/menu/edit';
  public const URL_PATH_DELETE = 'upsage/menu/delete';
  public const URL_PATH_DETAILS = 'upsage/menu/details';
  
  protected $urlBuilder;
  
  private $escaper;
  
  public function __construct(
   ContextInterface $context,
   UiComponentFactory $uiComponentFactory,
   UrlInterface $urlBuilder,
   array $components = [],
   array $data = []
  ) {
   $this->urlBuilder = $urlBuilder;
   parent::__construct($context, $uiComponentFactory, $components, $data);
  }
  
  public function prepareDataSource(array $dataSource) {
   if(isset($dataSource['data']['items'])) {
    foreach ($dataSource['data']['items'] as & $item) {
     if(isset($item['menu_id'])) {
      $title = $this->getEscaper()->escapeHtmlAttr($item['title']);
      $item[$this->getData('name')] = [
       'edit' => [
        'href' => $this->urlBuilder->getUrl(static::URL_PATH_EDIT,['menu_id' => $item['menu_id']]),
        'label' => __('Edit')
       ],
       'delete' => [
        'href' => $this->urlBuilder->getUrl(static::URL_PATH_DELETE,['menu_id' => $item['menu_id']]),
        'label' => __('Delete'),
        'confirm' => [
         'title' => __('Delete %1', $title),
         'message' => __('Are you sure you want to delete a %1 record?', $title)
        ],
        'post' => true
       ],
      ];
     }
    }
   }
   return $dataSource;
  }
  
  private function getEscaper() {
   if(!$this->escaper) {
    $this->escaper = ObjectManager::getInstance()->get(Escaper::class);
   }
   return $this->escaper;
  }

}
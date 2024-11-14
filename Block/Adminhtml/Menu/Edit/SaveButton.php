<?php

 namespace UpSage\Menu\Block\Adminhtml\Menu\Edit;
 
 use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
 use Magento\Ui\Component\Control\Container;
 
 class SaveButton extends GenericButton implements ButtonProviderInterface {
    
  public function getButtonData() {
   return [
    'label' => __('Save'),
    'class' => 'save primary',
    'data_attribute' => [
     'mage-init' => [
      'buttonAdapter' => [
       'actions' => [
        [
         'targetName' => 'upsage_menu_form.upsage_menu_form',
         'actionName' => 'save',
         'params' => [
          true,
          ['back' => 'continue']
         ]
        ]
       ]
      ]
     ]
    ],
    'class_name' => Container::SPLIT_BUTTON,
    'options' => $this->getOptions(),
    'dropdown_button_aria_label' => __('Save options'),
   ];
  }
  
  private function getOptions() {
   $options = [
    [
     'label' => __('Save & Duplicate'),
     'id_hard' => 'save_and_duplicate',
     'data_attribute' => [
      'mage-init' => [
       'buttonAdapter' => [
        'actions' => [
         [
          'targetName' => 'upsage_menu_form.upsage_menu_form',
          'actionName' => 'save',
          'params' => [
            true,
            ['back' => 'duplicate']
          ]
         ]
        ]
       ]
      ]
     ]
    ],
    [
     'id_hard' => 'save_and_close',
     'label' => __('Save & Close'),
     'data_attribute' => [
      'mage-init' => [
       'buttonAdapter' => [
        'actions' => [
         [
          'targetName' => 'upsage_menu_form.upsage_menu_form',
          'actionName' => 'save',
          'params' => [
           true,
           ['back' => 'close']
          ]
         ]
        ]
       ]
      ]
     ]
    ]
   ];
   return $options;
  }

 }

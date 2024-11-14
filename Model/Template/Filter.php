<?php

 namespace UpSage\Menu\Model\Template;
 
 class Filter extends \Magento\Email\Model\Template\Filter {
    
  public function mediaDirective($construction) {
   $params = $this->getParameters(html_entity_decode($construction[2], ENT_QUOTES));
   if(preg_match('/(^.*:\/\/.*|\.\.\/.*)/', $params['url'])) {
    throw new \InvalidArgumentException('Image path must be absolute and not include URLs');
   }
   return $this->_storeManager->getStore()->getBaseMediaDir() . '/' . $params['url'];
  }

 }
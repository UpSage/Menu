<?php

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
 
 class MenuByIdentifier extends AbstractBlock implements IdentityInterface {
    
  public const CACHE_KEY_PREFIX = 'MENU';
  
  private $menuByIdentifier;
  
  private $storeManager;
  
  private $filterProvider;
  
  private $menu;
  
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
  
  protected function _toHtml(): string {
   try {
    return $this->filterOutput(
     $this->getMenu()->getContent()
    );
   } catch (NoSuchEntityException $e) {
    return '';
   }
  }
  
  private function getIdentifier(): ?string {
   return $this->getData('identifier') ?: null;
  }
  
  private function filterOutput(string $content): string {
   return $this->filterProvider->getMenuFilter()->setStoreId($this->getCurrentStoreId())->filter($content);
  }
  
  private function getMenu(): MenuInterface {
   if(!$this->getIdentifier()) {
    throw new \InvalidArgumentException('Expected value of `identifier` was not provided');
   }
   if(null === $this->menu) {
    $this->menu = $this->menuByIdentifier->execute(
     (string)$this->getIdentifier(),
     $this->getCurrentStoreId()
    );
    if(!$this->menu->isActive()) {
     throw new NoSuchEntityException(
      __('The menu with identifier "%identifier" is not enabled.', $this->getIdentifier())
     );
    }
   }
   return $this->menu;
  }
  
  private function getCurrentStoreId(): int {
   return (int)$this->storeManager->getStore()->getId();
  }
  
  public function getIdentities(): array {
   if(!$this->getIdentifier()) {
    return [];
   }
   $identities = [
    self::CACHE_KEY_PREFIX . '_' . $this->getIdentifier(),
    self::CACHE_KEY_PREFIX . '_' . $this->getIdentifier() . '_' . $this->getCurrentStoreId()
   ];
   try {
    $menu = $this->getMenu();
    if($menu instanceof IdentityInterface) {
     $identities = array_merge($identities, $menu->getIdentities());
    }
   } catch (NoSuchEntityException $e) {}
   
   return $identities;
  }

 }

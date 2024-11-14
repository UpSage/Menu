<?php

 namespace UpSage\Menu\Model;
 
 use UpSage\Menu\Api\Data\MenuInterface;
 use Magento\Framework\App\ObjectManager;
 use Magento\Framework\DataObject\IdentityInterface;
 use Magento\Framework\Model\AbstractModel;
 use Magento\Framework\Validation\ValidationException;
 use Magento\Framework\Validator\HTML\WYSIWYGValidatorInterface;
 use Magento\Framework\Model\Context;
 use Magento\Framework\Registry;
 use Magento\Framework\Model\ResourceModel\AbstractResource;
 use Magento\Framework\Data\Collection\AbstractDb;
 use Magento\Backend\Model\Validator\UrlKey\CompositeUrlKey;
 use Magento\Framework\Exception\LocalizedException;
 
 class Menu extends AbstractModel implements MenuInterface, IdentityInterface {
    
  public const CACHE_TAG = 'menu_b';
  
  public const STATUS_ENABLED = 1;
  
  public const STATUS_DISABLED = 0;
  
  protected $_cacheTag = self::CACHE_TAG;
  
  protected $_eventPrefix = 'menu';
  
  private $wysiwygValidator;
  
  private $compositeUrlValidator;
  
  public function __construct(
   Context $context,
   Registry $registry,
   AbstractResource $resource = null,
   AbstractDb $resourceCollection = null,
   array $data = [],
   ?WYSIWYGValidatorInterface $wysiwygValidator = null,
   CompositeUrlKey $compositeUrlValidator = null
  ) {
   parent::__construct($context, $registry, $resource, $resourceCollection, $data);
   $this->wysiwygValidator = $wysiwygValidator
   ?? ObjectManager::getInstance()->get(WYSIWYGValidatorInterface::class);
   $this->compositeUrlValidator = $compositeUrlValidator
   ?? ObjectManager::getInstance()->get(CompositeUrlKey::class);
  }
  
  protected function _construct() {
   $this->_init(\UpSage\Menu\Model\ResourceModel\Menu::class);
  }
  
  public function beforeSave() {
   if($this->hasDataChanges()) {
    $this->setUpdateTime(null);
   }
   $needle = 'menu_id="' . $this->getId() . '"';
   $content = ($this->getContent() !== null) ? $this->getContent() : '';
   if(strpos($content, $needle) !== false) {
    throw new \Magento\Framework\Exception\LocalizedException(
     __('Make sure that menu content does not reference the menu itself.')
    );
   }
   $errors = $this->compositeUrlValidator->validate($this->getIdentifier());
   if(!empty($errors)) {
    throw new LocalizedException($errors[0]);
   }
   parent::beforeSave();
   if($content && $content !== $this->getOrigData(self::CONTENT)) {
    try {
     $this->wysiwygValidator->validate($content);
    } catch (ValidationException $exception) {
     throw new ValidationException(
      __('Content field contains restricted HTML elements. %1', $exception->getMessage()),
      $exception
     );
    }
   }
   return $this;
  }
  
  public function getIdentities() {
   return [self::CACHE_TAG . '_' . $this->getId(), self::CACHE_TAG . '_' . $this->getIdentifier()];
  }
  
  public function getId() {
   return $this->getData(self::MENU_ID);
  }
  
  public function getIdentifier() {
   return (string)$this->getData(self::IDENTIFIER);
  }
  
  public function getTitle() {
   return $this->getData(self::TITLE);
  }
  
  public function getContent() {
   return $this->getData(self::CONTENT);
  }
  
  public function getCreationTime() {
   return $this->getData(self::CREATION_TIME);
  }
  
  public function getUpdateTime() {
   return $this->getData(self::UPDATE_TIME);
  }
  
  public function isActive() {
   return (bool)$this->getData(self::IS_ACTIVE);
  }
  
  public function setId($id) {
   return $this->setData(self::MENU_ID, $id);
  }
  
  public function setIdentifier($identifier) {
   return $this->setData(self::IDENTIFIER, $identifier);
  }
  
  public function setTitle($title) {
   return $this->setData(self::TITLE, $title);
  }
  
  public function setContent($content) {
   return $this->setData(self::CONTENT, $content);
  }
  
  public function setCreationTime($creationTime) {
   return $this->setData(self::CREATION_TIME, $creationTime);
  }
  
  public function setUpdateTime($updateTime) {
   return $this->setData(self::UPDATE_TIME, $updateTime);
  }
  
  public function setIsActive($isActive) {
   return $this->setData(self::IS_ACTIVE, $isActive);
  }
  
  public function getStores() {
   return $this->hasData('stores') ? $this->getData('stores') : $this->getData('store_id');
  }
  
  public function getAvailableStatuses() {
   return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
  }

 }
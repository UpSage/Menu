<?php

 namespace UpSage\Menu\Api\Data;
 
 interface MenuInterface {
    
  const MENU_ID       = 'menu_id';
  const IDENTIFIER    = 'identifier';
  const TITLE         = 'title';
  const CONTENT       = 'content';
  const CREATION_TIME = 'creation_time';
  const UPDATE_TIME   = 'update_time';
  const IS_ACTIVE     = 'is_active';
  
  public function getId();
  
  public function getIdentifier();
  
  public function getTitle();
  
  public function getContent();
  
  public function getCreationTime();
  
  public function getUpdateTime();
  
  public function isActive();
  
  public function setId($id);
  
  public function setIdentifier($identifier);
  
  public function setTitle($title);
  
  public function setContent($content);
  
  public function setCreationTime($creationTime);
  
  public function setUpdateTime($updateTime);
  
  public function setIsActive($isActive);

 }

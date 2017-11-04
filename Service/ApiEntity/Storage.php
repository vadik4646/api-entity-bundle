<?php

namespace Vadik4646\EntityApiBundle\Service\ApiEntity;

class Storage implements StorageInterface
{
  /** @var array $registeredEntities */
  private $registeredEntities;

  public function __construct($registeredEntities)
  {
    $this->registeredEntities = $registeredEntities;
  }

  /**
   * @param string $name
   * @return string
   */
  public function get($name)
  {
    return $this->registeredEntities[$name];
  }

  /**
   * @param string $name
   * @return bool
   */
  public function isEntityRegistered($name)
  {
    if (!array_key_exists($name, $this->registeredEntities)) {
      return false;
    }

    if (!class_exists($this->registeredEntities[$name])) {
      return false;
    }

    return true;
  }
}

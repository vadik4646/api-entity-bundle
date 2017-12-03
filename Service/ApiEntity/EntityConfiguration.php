<?php

namespace Vadik4646\EntityApiBundle\Service\ApiEntity;

use Vadik4646\EntityApiBundle\Service\ApiEntity\Configuration\ConfigurationBag;
use Vadik4646\EntityApiBundle\Service\ApiEntity\Configuration\FieldConfiguration;

class EntityConfiguration
{
  /** @var ConfigurationBag */
  private $configurationBag;
  private $entityName;
  private $entityAlias;

  public function __construct(ConfigurationBag $configurationBag, $entityName)
  {
    $this->configurationBag = $configurationBag;
    $this->entityName = $entityName;
    $this->configurationBag->processEntityConfiguration($entityName);
  }

  /**
   * @return null|string
   */
  public function getEntityClass()
  {
    return $this->configurationBag->getEntityClass($this->entityName);
  }

  /**
   * @return null|string
   */
  public function getPkKey()
  {
    return $this->configurationBag->getPkField($this->entityName)->getName();
  }

  /**
   * @param $entity
   * @return EntityConfiguration
   */
  public function create($entity)
  {
    return new self($this->configurationBag, $entity);
  }

  /**
   * @return string
   */
  public function getEntityAlias()
  {
    if (!$this->entityAlias) {
      $this->entityAlias = $this->configurationBag->generateAlias($this->entityName);
    }
    return $this->entityAlias;
  }

  /**
   * @return string
   */
  public function getEntityName()
  {
    return $this->entityName;
  }

  /**
   * @return FieldConfiguration[]
   */
  public function getRelationFields()
  {
    return $this->configurationBag->getRelationFields($this->entityName);
  }

  /**
   * @param $relation
   * @return FieldConfiguration
   */
  public function getRelationField($relation)
  {
    return $this->configurationBag->getRelationField($this->entityName, $relation);
  }

  /**
   * @return FieldConfiguration[]
   */
  public function getBuilders()
  {
    return $this->configurationBag->getBuilders($this->entityName);
  }
}

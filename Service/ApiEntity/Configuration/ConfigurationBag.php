<?php

namespace Vadik4646\EntityApiBundle\Service\ApiEntity\Configuration;

use Doctrine\Common\Annotations\AnnotationReader;
use ReflectionClass;
use ReflectionProperty;
use Vadik4646\EntityApiBundle\Annotations\Entity;
use Vadik4646\EntityApiBundle\Service\ApiEntity\FieldType;
use Vadik4646\EntityApiBundle\Service\ApiEntity\Storage;

class ConfigurationBag
{
  private static $fields = [];
  private static $filters = [];
  private static $entities = [];
  private static $classPaths = [];
  private static $entityAliases = [];

  private $storage;

  public function __construct(Storage $storage)
  {
    $this->storage = $storage;
  }

  /**
   * @param $entityName
   */
  public function processEntityConfiguration($entityName)
  {
    if (!$classPath = $this->storage->get($entityName)) {
      return ; // todo trow an error
    }

    self::$classPaths[$entityName] = $classPath;
    $this->processFields($entityName, $classPath);
    $this->processFilters($entityName, $classPath);
    $this->processTable($entityName, $classPath);
  }

  /**
   * Parse table annotations on registered entities
   *
   * @param $entityName
   * @return string
   */
  public function generateAlias($entityName)
  {
    $entityFirstLetter = $entityName[0];
    $postfix = 0;
    while (
      array_search($entityFirstLetter . $postfix, self::$entityAliases) !== false &&
      ++$postfix < 100
    ) {
    }
    $entityAlias = $entityFirstLetter . $postfix;
    self::$entityAliases[$entityName] = $entityAlias;

    return $entityAlias;
  }

  /**
   * @param string $entityName
   * @return string|null
   */
  public function getEntityClass($entityName)
  {
    return array_key_exists($entityName, self::$classPaths) ? self::$classPaths[$entityName] : null;
  }

  /**
   * @param $entityName
   * @return FieldConfiguration[]
   */
  public function getRelationFields($entityName)
  {
    $relationFields = [];
    foreach (self::$fields[$entityName] as $field) {
      /** @var $field FieldConfiguration */
      if ($field->isRelation()) {
        $relationFields[] = $field;
      }
    }

    return $relationFields;
  }

  /**
   * @param $entityName
   * @param $relationName
   * @return FieldConfiguration
   */
  public function getRelationField($entityName, $relationName)
  {
    foreach (self::$fields[$entityName] as $field) {
      /** @var $field FieldConfiguration */
      if ($field->isRelation() && $field->getName() === $relationName) {
        return $field;
      }
    }

    return null;
  }

  /**
   * @param $entityName
   * @return FieldConfiguration
   */
  public function getPkField($entityName)
  {
    foreach (self::$fields[$entityName] as $field) {
      /** @var $field FieldConfiguration */
      if ($field->isPk()) {
        return $field;
      }
    }

    return null;
  }

  /**
   * Parse field annotations on registered entities
   *
   * @param string $classPath
   * @param array  $fields
   * @return array
   */
  private function process($classPath, $fields)
  {
    $reader = new AnnotationReader();
    $reflectionClass = new ReflectionClass($classPath);
    $entityFields = [];
    foreach ($reflectionClass->getProperties() as $reflectionProperty) {
      $reflectionProp = new ReflectionProperty($classPath, $reflectionProperty->getName());
      foreach ($fields as $field) {
        $entityField = $reader->getPropertyAnnotation($reflectionProp, $field);
        if ($entityField) {
          $entityField->name = $reflectionProp->name;
          $entityFields[] = $entityField;
        }
      }
    }

    return $entityFields;
  }

  /**
   * @param string $entityName
   * @param string $classPath
   */
  private function processFields($entityName, $classPath) // todo add field from method
  {
    if (!array_key_exists($entityName, self::$fields)) {
      self::$fields[$entityName] = [];
      foreach ($this->process($classPath, FieldType::fieldMap()) as $field) {
        self::$fields[$entityName][] = new FieldConfiguration($field);
      }
    }
  }

  /**
   * @param string $entityName
   * @param string $classPath
   */
  private function processFilters($entityName, $classPath)
  {
    if (!array_key_exists($entityName, self::$filters)) {
      self::$filters[$entityName] = [];
      foreach ($this->process($classPath, FieldType::filterMap()) as $filter) {
        self::$filters[$entityName][] = new FilterConfiguration($filter);
      }
    }
  }

  /**
   * Parse table annotations on registered entities
   *
   * @param string $entityName
   * @param string $classPath
   */
  private function processTable($entityName, $classPath)
  {
    $reader = new AnnotationReader();
    $reflectionClass = new \ReflectionClass($classPath);
    $entity = $reader->getClassAnnotation($reflectionClass, Entity::class);
    self::$entities[$entityName] = new EntityConfiguration($entity);
  }
}

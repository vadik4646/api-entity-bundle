<?php

namespace Vadik4646\EntityApiBundle\Service\ApiEntity;

use Doctrine\Common\Annotations\AnnotationReader;
use Vadik4646\EntityApiBundle\Annotations\Field;
use Vadik4646\EntityApiBundle\Annotations\ManyToMany;
use Vadik4646\EntityApiBundle\Annotations\ManyToOne;
use Vadik4646\EntityApiBundle\Annotations\OneToMany;
use Vadik4646\EntityApiBundle\Annotations\OneToOne;
use Vadik4646\EntityApiBundle\Annotations\PrimaryKey;
use Vadik4646\EntityApiBundle\Annotations\Table;
use Vadik4646\EntityApiBundle\Service\ApiEntity\Relation\RelationFactory;
use Vadik4646\EntityApiBundle\Service\ApiEntity\Relation\RelationInterface;
use Vadik4646\EntityApiBundle\Utils\RelationField;

class EntityConfiguration
{
  private static $entities = [];
  private $table;
  /** @var Field[] */
  private $fields;
  private $entityClass;
  private $entityName;
  private $storage;

  public function __construct(Storage $storage, $entityName = null)
  {
    $this->storage = $storage;
    if ($entityName) {
      $this->loadEntityConfig($entityName);
    }
  }

  /**
   * @param string $entityName
   * @return EntityConfiguration
   */
  public function create($entityName)
  {
    return new self($this->storage, $entityName);
  }

  /**
   * @return string
   */
  public function getTableName()
  {
    return $this->table->name;
  }

  /**
   * @return string
   */
  public function getPkKey()
  {
    return $this->getPkKeyField()->name;
  }

  /**
   * @return null|Field
   */
  public function getPkKeyField()
  {
    foreach ($this->fields as $field) {
      if ($field instanceof PrimaryKey) {
        return $field;
      }
    }

    return null;
  }

  /**
   * @return string
   */
  public function getEntityClass()
  {
    return $this->entityClass;
  }

  /**
   * @return string
   */
  public function getTableAlias()
  {
    return $this->table->name[0];
  }

  /**
   * @param string $entityName
   */
  private function loadEntityConfig($entityName)
  {
    $this->entityName = $entityName;
    $this->entityClass = $this->storage->get($this->entityName);

    if (array_key_exists($entityName, self::$entities)) {
      $this->table = self::$entities[$entityName]['table'];
      $this->fields = self::$entities[$entityName]['fields'];
    } else {
      $this->processTable();
      $this->processFields();
    }
  }

  public function hasRelationWith(EntityConfiguration $entityConfiguration)
  {
    $relationName = $entityConfiguration->getEntityName();

    return $this->getFieldByEntityName($relationName) instanceof RelationField;
  }

  public function getRelationField(EntityConfiguration $relationEntityConfiguration)
  {
    $relationName = $relationEntityConfiguration->getEntityName();

    return $this->getFieldByEntityName($relationName);
  }

  /**
   * @param string $relationName
   * @return null|RelationInterface
   */
  public function getRelationWith($relationName)
  {
    $field = $this->getFieldByEntityName($relationName);

    if ($field instanceof OneToMany) {
      return RelationFactory::get(RelationInterface::ONE_TO_MANY);
    } elseif ($field instanceof ManyToOne) {
      return RelationFactory::get(RelationInterface::MANY_TO_ONE);
    } elseif ($field instanceof ManyToMany) {
      return RelationFactory::get(RelationInterface::MANY_TO_MANY);
    } elseif ($field instanceof OneToOne) {
      return RelationFactory::get(RelationInterface::ONE_TO_ONE);
    }

    return null;
  }

  public function getEntityName()
  {
    return $this->entityName;
  }

  public function getFieldByEntityName($entityKey)
  {
    foreach ($this->fields as $field) { // todo move to relations bag, field bags
      if ($field instanceof RelationField && $field->entity === $entityKey) {
        return $field;
      }
    }

    return null;
  }

  /**
   * @param string $fieldName
   * @return null|Field
   */
  public function getFieldByFieldName($fieldName)
  {
    foreach ($this->fields as $field) { // todo move to relations bag, field bags
      if ($field instanceof RelationField && $field->name === $fieldName) {
        return $field;
      }
    }

    return null;
  }

  /**
   * @return Field[]
   */
  public function getFields()
  {
    return $this->fields;
  }

  /**
   * @param string $key
   * @return null|RelationField
   */
  public function getRelationFieldByColumn($key)
  {
    foreach ($this->fields as $field) {
      if ($field instanceof RelationField && $field->name === $key) { // todo $field->field !?
        return $field;
      }
    }

    return null;
  }

  /**
   * Parse table annotations on registered entities
   */
  private function processTable()
  {
    $reader = new AnnotationReader();
    $reflectionClass = new \ReflectionClass($this->entityClass);
    $this->table = $reader->getClassAnnotation($reflectionClass, Table::class); // todo only on php 7
  }

  /**
   * Parse field annotations on registered entities
   */
  private function processFields()
  {
    $reader = new AnnotationReader();
    $reflectionClass = new \ReflectionClass($this->entityClass);

    $fieldTypes = FieldType::map();
    foreach ($reflectionClass->getProperties() as $reflectionProperty) {
      $reflectionProp = new \ReflectionProperty($this->entityClass, $reflectionProperty->getName());
      foreach ($fieldTypes as $fieldType) {
        $publicField = $reader->getPropertyAnnotation($reflectionProp, $fieldType);
        if ($publicField) {
          $publicField->name = $reflectionProp->name;
          $this->fields[] = $publicField;
        }
      }
    }
  }
}

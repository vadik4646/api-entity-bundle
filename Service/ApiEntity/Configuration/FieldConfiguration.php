<?php

namespace Vadik4646\EntityApiBundle\Service\ApiEntity\Configuration;

use Vadik4646\EntityApiBundle\Annotations\PrimaryKey;
use Vadik4646\EntityApiBundle\Utils\RelationField;

class FieldConfiguration
{
  private $field;

  public function __construct($field)
  {
    $this->field = $field;
  }

  /**
   * @return bool
   */
  public function isRelation()
  {
    return $this->field instanceof RelationField;
  }

  /**
   * @return bool
   */
  public function isPk()
  {
    return $this->field instanceof PrimaryKey;
  }

  /**
   * @return string
   */
  public function getName()
  {
    return $this->field->name;
  }

  /**
   * @return string
   */
  public function getEntity()
  {
    if (!$this->field instanceof RelationField) {
      return null;
    }

    return $this->field->entity;
  }
}

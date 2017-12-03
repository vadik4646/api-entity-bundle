<?php

namespace Vadik4646\EntityApiBundle\Service\ApiEntity;

class FieldType
{
  const FIELD         = 'Vadik4646\EntityApiBundle\Annotations\Field';
  const MANY_TO_MANY  = 'Vadik4646\EntityApiBundle\Annotations\ManyToMany';
  const MANY_TO_ONE   = 'Vadik4646\EntityApiBundle\Annotations\ManyToOne';
  const ONE_TO_MANY   = 'Vadik4646\EntityApiBundle\Annotations\OneToMany';
  const ONE_TO_ONE    = 'Vadik4646\EntityApiBundle\Annotations\OneToOne';
  const PRIMARY_KEY   = 'Vadik4646\EntityApiBundle\Annotations\PrimaryKey';
  const FIELD_BUILDER = 'Vadik4646\EntityApiBundle\Annotations\FieldBuilder';
  const FIELD_FILTER  = 'Vadik4646\EntityApiBundle\Annotations\FieldFilter';

  const TYPE_FILTER  = 'filter';
  const TYPE_BUILDER = 'builder';
  const TYPE_FIELD   = 'field';

  /**
   * @return array
   */
  public static function fieldMap()
  {
    return [
      self::PRIMARY_KEY,
      self::FIELD,
      self::ONE_TO_ONE,
      self::MANY_TO_ONE,
      self::ONE_TO_MANY,
      self::MANY_TO_MANY
    ];
  }

  /**
   * @return array
   */
  public static function filterMap()
  {
    return [
      self::FIELD_FILTER
    ];
  }

  /**
   * @return array
   */
  public static function typeMap()
  {
    return [
      self::TYPE_FILTER  => self::filterMap(),
      self::TYPE_FIELD   => self::fieldMap()
    ];
  }
}

<?php

namespace Vadik4646\EntityApiBundle\Service\ApiEntity;

class FieldType
{
  const FIELD        = 'Vadik4646\EntityApiBundle\Annotations\Field';
  const MANY_TO_MANY = 'Vadik4646\EntityApiBundle\Annotations\ManyToMany';
  const MANY_TO_ONE  = 'Vadik4646\EntityApiBundle\Annotations\ManyToOne';
  const ONE_TO_MANY  = 'Vadik4646\EntityApiBundle\Annotations\OneToMany';
  const ONE_TO_ONE   = 'Vadik4646\EntityApiBundle\Annotations\OneToOne';
  const PRIMARY_KEY  = 'Vadik4646\EntityApiBundle\Annotations\PrimaryKey';

  /**
   * @return array
   */
  public static function map()
  {
    return [self::PRIMARY_KEY, self::FIELD, self::ONE_TO_ONE, self::MANY_TO_ONE, self::ONE_TO_MANY, self::MANY_TO_MANY];
  }
}

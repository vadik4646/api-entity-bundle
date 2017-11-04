<?php

namespace Vadik4646\EntityApiBundle\Annotations;

use Vadik4646\EntityApiBundle\Utils\RelationField;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
class OneToMany extends RelationField
{
  public $name;
}

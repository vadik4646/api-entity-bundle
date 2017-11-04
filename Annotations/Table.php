<?php

namespace Vadik4646\EntityApiBundle\Annotations;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target("CLASS")
 */
class Table
{
  public $name;
}

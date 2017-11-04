<?php

namespace Vadik4646\EntityApiBundle\Annotations;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
class Field
{
  public $name = null; // todo import data from doctrine annotations...
}

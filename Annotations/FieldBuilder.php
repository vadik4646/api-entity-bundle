<?php

namespace Vadik4646\EntityApiBundle\Annotations;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
class FieldBuilder
{
  /**
   * @var string
   */
  public $method;

  /**
   * @var string
   */
  public $class;
}

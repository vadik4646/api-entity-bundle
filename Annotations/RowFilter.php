<?php

namespace Vadik4646\EntityApiBundle\Annotations;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
class RowFilter
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

<?php

namespace Vadik4646\EntityApiBundle\Utils;

interface FieldFilterInterface
{
  /**
   * @param $entityRow
   * @return bool
   */
  public function handle($entityRow);
}

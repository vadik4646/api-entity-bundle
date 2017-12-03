<?php

namespace Vadik4646\EntityApiBundle\Utils;

interface FieldBuilderInterface
{
  /**
   * @param $entityRow
   * @return bool
   */
  public function handle($entityRow);
}

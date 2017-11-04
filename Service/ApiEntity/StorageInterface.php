<?php

namespace Vadik4646\EntityApiBundle\Service\ApiEntity;

interface StorageInterface
{
  public function get($name);

  public function isEntityRegistered($name);
}

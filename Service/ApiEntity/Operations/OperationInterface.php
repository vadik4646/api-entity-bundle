<?php

namespace Vadik4646\EntityApiBundle\Service\ApiEntity\Operations;

use Vadik4646\EntityApiBundle\Service\ApiEntity\ParamProviderTree;

interface OperationInterface
{
  public function handleFromParamTree(ParamProviderTree $paramProviderTree);
}

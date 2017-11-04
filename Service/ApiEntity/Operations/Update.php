<?php

namespace Vadik4646\EntityApiBundle\Service\ApiEntity\Operations;

use Doctrine\ORM\EntityManager;
use Vadik4646\EntityApiBundle\Service\ApiEntity\ParamProviderTree;

class Update implements OperationInterface
{
  private $entityManager;

  public function __construct(EntityManager $entityManager)
  {
    $this->entityManager = $entityManager;
  }

  public function handleFromParamTree(ParamProviderTree $paramProviderTree)
  {
    // TODO: Implement handle() method.
  }
}

<?php

namespace Vadik4646\EntityApiBundle\Service\ApiEntity;

use Vadik4646\EntityApiBundle\Service\ApiEntity\Operations\OperationFactory;

class ApiEntity
{
  private $operationFactory;
  private $paramProviderTree;

  public function __construct(OperationFactory $operationFactory, ParamProviderTree $paramProviderTree)
  {
    $this->operationFactory = $operationFactory;
    $this->paramProviderTree = $paramProviderTree;
  }

  /**
   * @param string $entity
   * @param string $method
   * @param array  $params
   * @return Response
   */
  public function get($entity, $method, $params)
  {
    $paramProvider = $this->paramProviderTree->create($params, $entity);
    $operation = $this->operationFactory->get($method);

    return new Response($operation->handleFromParamTree($paramProvider));
  }
}

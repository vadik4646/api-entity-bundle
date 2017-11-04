<?php

namespace Vadik4646\EntityApiBundle\Service\ApiEntity\Operations;

use Vadik4646\EntityApiBundle\Service\ApiEntity\DataProvider;
use Vadik4646\EntityApiBundle\Service\ApiEntity\EntityConfiguration;
use Vadik4646\EntityApiBundle\Service\ApiEntity\ResultManager;

class OperationFactory
{
  private $entityConfiguration;
  private $dataProvider;

  private $operations = [
    'GET'    => Select::class,
    'POST'   => Insert::class,
    'PATCH'  => Update::class,
    'DELETE' => Delete::class
  ];

  public function __construct(
    DataProvider $dataProvider,
    EntityConfiguration $entityConfiguration
  ) {
    $this->dataProvider = $dataProvider;
    $this->entityConfiguration = $entityConfiguration;
  }

  /**
   * @param $operation
   * @return bool|OperationInterface
   */
  public function get($operation)
  {
    if (!array_key_exists($operation, $this->operations)) {
      return false;
    }

    return new $this->operations[$operation]($this->dataProvider, $this->entityConfiguration);
  }
}

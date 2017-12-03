<?php

namespace Vadik4646\EntityApiBundle\Service\ApiEntity\Operations;

use Vadik4646\EntityApiBundle\Service\ApiEntity\Configuration\ConfigurationBag;
use Vadik4646\EntityApiBundle\Service\ApiEntity\DataProvider;

class OperationFactory
{
  private $configurationBag;
  private $dataProvider;

  private $operations = [
    'GET'    => Select::class,
    'POST'   => Insert::class,
    'PATCH'  => Update::class,
    'DELETE' => Delete::class
  ];

  public function __construct(
    DataProvider $dataProvider,
    ConfigurationBag $configurationBag
  ) {
    $this->dataProvider = $dataProvider;
    $this->configurationBag = $configurationBag;
  }

  /**
   * @param $operation
   * @return null|OperationInterface
   */
  public function get($operation)
  {
    if (!array_key_exists($operation, $this->operations)) {
      return null;
    }

    return new $this->operations[$operation]($this->dataProvider, $this->configurationBag);
  }
}

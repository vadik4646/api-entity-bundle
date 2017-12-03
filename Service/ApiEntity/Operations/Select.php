<?php

namespace Vadik4646\EntityApiBundle\Service\ApiEntity\Operations;

use Vadik4646\EntityApiBundle\Service\ApiEntity\Configuration\ConfigurationBag;
use Vadik4646\EntityApiBundle\Service\ApiEntity\DataProvider;
use Vadik4646\EntityApiBundle\Service\ApiEntity\EntityConfiguration;
use Vadik4646\EntityApiBundle\Service\ApiEntity\ParamProviderTree;
use Vadik4646\EntityApiBundle\Service\ApiEntity\ResultManager;

class Select implements OperationInterface
{
  private $configurationBag;
  private $dataProvider;

  public function __construct(DataProvider $dataProvider, ConfigurationBag $configurationBag)
  {
    $this->dataProvider = $dataProvider;
    $this->configurationBag = $configurationBag;
  }

  /**
   * @param ParamProviderTree $paramProviderTree
   * @return ResultManager|null
   */
  public function handleFromParamTree(ParamProviderTree $paramProviderTree)
  {
    $entityConfiguration = new EntityConfiguration($this->configurationBag, $paramProviderTree->getEntity());

    return $this->dataProvider->getData($paramProviderTree, $entityConfiguration)
      ->setParamProviderTree($paramProviderTree)
      ->setEntityConfiguration($entityConfiguration);
  }
}

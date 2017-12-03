<?php

namespace Vadik4646\EntityApiBundle\Service\ApiEntity;

class ResultManager
{
  private $result;
  /** @var EntityConfiguration */
  private $entityConfiguration;
  /** @var ParamProviderTree */
  private $paramProviderTree;

  public function __construct($result)
  {
    $this->result = $result;
  }

  /**
   * @param EntityConfiguration $entityConfiguration
   * @return $this
   */
  public function setEntityConfiguration(EntityConfiguration $entityConfiguration)
  {
    $this->entityConfiguration = $entityConfiguration;

    return $this;
  }

  /**
   * @param ParamProviderTree $paramProviderTree
   * @return $this
   */
  public function setParamProviderTree(ParamProviderTree $paramProviderTree)
  {
    $this->paramProviderTree = $paramProviderTree;

    return $this;
  }

  /**
   * @return ParamProviderTree
   */
  public function getParamProviderTree()
  {
    return $this->paramProviderTree;
  }

  /**
   * @return EntityConfiguration
   */
  public function getEntityConfiguration()
  {
    return $this->entityConfiguration;
  }

  /**
   * @return array
   */
  public function getResult()
  {
    $filterManager = new FilterManager();
    $filterManager->filter($this->result, $this->entityConfiguration);

    return $this->result;
  }
}

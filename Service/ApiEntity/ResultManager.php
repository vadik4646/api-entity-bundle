<?php

namespace Vadik4646\EntityApiBundle\Service\ApiEntity;

class ResultManager
{
  private $result;
  private $entityConfiguration;
  private $paramProviderTree;
  private $relationData = [];

  public function __construct($result)
  {
    $this->result = $result;
  }

  /**
   * @param string        $relationKey
   * @param ResultManager $data
   */
  public function setRelationData($relationKey, $data)
  {
    $this->relationData[$relationKey] = $data;
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
   * @return ResultManager[]
   */
  public function getRelationData()
  {
    return $this->relationData;
  }

  /**
   * @return array
   */
  public function getResult()
  {
    return $this->result;
  }

  /**
   * @param string $key
   * @param string $value
   * @return array
   */
  public function findRowsBy($key, $value)
  {
    $result = [];
    foreach ($this->result as $row) {
      if ($row[$key] == $value) {
        $result[] = $row;
      }
    }

    return $result;
  }

  /**
   * @param string $key
   * @param string $value
   * @return array|null
   */
  public function findRowBy($key, $value)
  {
    foreach ($this->result as $row) {
      if ($row[$key] == $value) {
        return $row;
      }
    }

    return null;
  }
}

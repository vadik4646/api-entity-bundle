<?php

namespace Vadik4646\EntityApiBundle\Service\ApiEntity\Operations;

use Vadik4646\EntityApiBundle\Service\ApiEntity\DataProvider;
use Vadik4646\EntityApiBundle\Service\ApiEntity\EntityConfiguration;
use Vadik4646\EntityApiBundle\Service\ApiEntity\ParamProviderTree;
use Vadik4646\EntityApiBundle\Service\ApiEntity\ResultManager;

class Select implements OperationInterface
{
  private $entityConfiguration;
  private $dataProvider;

  public function __construct(DataProvider $dataProvider, EntityConfiguration $entityConfiguration)
  { // todo storage interface ??? do I need it ?
    $this->dataProvider = $dataProvider;
    $this->entityConfiguration = $entityConfiguration;
  }

  /**
   * @param ParamProviderTree $paramProviderTree
   * @return ResultManager|null
   */
  public function handleFromParamTree(ParamProviderTree $paramProviderTree)
  {
    $entityConfiguration = $this->getEntityConfiguration($paramProviderTree->getEntity()); // todo move to ::HAS
    if (is_null($entityConfiguration)) {
      return null;
    }

    $paramProviderTree->setPkKey($entityConfiguration->getPkKey());

    $result = $this->dataProvider->getData($paramProviderTree, $entityConfiguration)
      ->setEntityConfiguration($entityConfiguration)
      ->setParamProviderTree($paramProviderTree);

    $paramProviderTree->setResultManager($result);
    $this->handleRelationsData($paramProviderTree, $entityConfiguration, $result);

    return $result;
  }

  /**
   * @param $entityName
   * @return EntityConfiguration
   */
  private function getEntityConfiguration($entityName)
  {
    return $this->entityConfiguration->create($entityName);
  }

  /**
   * @param ParamProviderTree   $paramProviderTree
   * @param EntityConfiguration $entityConfiguration
   * @param ResultManager       $resultManager
   */
  private function handleRelationsData(
    ParamProviderTree $paramProviderTree,
    EntityConfiguration $entityConfiguration,
    ResultManager $resultManager
  ) {
    $requestedRelations = $paramProviderTree->getRelations();
    foreach ($requestedRelations as $requestedRelation) {
      $relationData = $this->getRelationData(
        $paramProviderTree,
        $entityConfiguration,
        $requestedRelation
      );
      $resultManager->setRelationData($requestedRelation, $relationData);
    }
  }

  /**
   * @param ParamProviderTree   $paramProviderTree
   * @param EntityConfiguration $entityConfiguration
   * @param string              $requestedRelation
   * @return ResultManager|null
   */
  private function getRelationData(
    ParamProviderTree $paramProviderTree,
    EntityConfiguration $entityConfiguration,
    $requestedRelation
  ) {
    $field = $entityConfiguration->getRelationFieldByColumn($requestedRelation);
    $relationEntityConfiguration = $entityConfiguration->create($field->entity);

    if ($entityConfiguration->hasRelationWith($relationEntityConfiguration)) {
      $relationParamProviderTree = $entityConfiguration->getRelationWith($field->entity)->configureParams(
        $paramProviderTree,
        $entityConfiguration,
        $relationEntityConfiguration,
        $requestedRelation
      );

      return $this->handleFromParamTree($relationParamProviderTree);
    }

    return null;
  }
}

<?php

namespace Vadik4646\EntityApiBundle\Service\ApiEntity\Relation;

use Vadik4646\EntityApiBundle\Service\ApiEntity\EntityConfiguration;
use Vadik4646\EntityApiBundle\Service\ApiEntity\ParamProviderTree;

class OneToMany
{
  public function configureParams(
    ParamProviderTree $paramProviderTree,
    EntityConfiguration $entityConfiguration,
    EntityConfiguration $requestedEntityConfiguration,
    $requestedRelation
  ) {
    $requestedEntityName = $requestedEntityConfiguration->getEntityName();
    $relationParamProviderTree = $paramProviderTree->createFromRelation($requestedRelation, $requestedEntityName);
    $relationCriteria = $this->buildCriteria($paramProviderTree, $entityConfiguration, $requestedEntityConfiguration);
    $relationParamProviderTree->appendCriteria($relationCriteria);

    return $relationParamProviderTree;
  }

  private function buildCriteria(
    ParamProviderTree $paramProviderTree,
    EntityConfiguration $entityConfiguration,
    EntityConfiguration $requestedEntityConfiguration
  ) {
    $primaryKeys = [];
    foreach ($paramProviderTree->getResultManager()->getResult() as $result) {
      $primaryKeys[] = $result[$entityConfiguration->getPkKey()];
    }

    $field = $requestedEntityConfiguration->getFieldByEntityName($entityConfiguration->getEntityName());

    // todo $field->field refactor! move to key || name
    return [
      $field->name,
      'in',
      $primaryKeys
    ]; // todo "in" remove magic
  }
}

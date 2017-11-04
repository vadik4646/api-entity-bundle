<?php
/**
 * Created by PhpStorm.
 * User: vtabaran
 * Date: 10/23/17
 * Time: 1:01 AM
 */

namespace Vadik4646\EntityApiBundle\Service\ApiEntity\Relation;

use Vadik4646\EntityApiBundle\Service\ApiEntity\EntityConfiguration;
use Vadik4646\EntityApiBundle\Service\ApiEntity\ParamProviderTree;

class ManyToOne
{
  public function configureParams(
    ParamProviderTree $paramProviderTree,
    EntityConfiguration $entityConfiguration,
    EntityConfiguration $requestedEntityConfiguration,
    $requestedRelation
  ) {
    $relationParamProviderTree = $paramProviderTree->createFromRelation(
      $requestedRelation,
      $requestedEntityConfiguration->getEntityName()
    );
    $relationCriteria = $this->buildCriteria($paramProviderTree, $entityConfiguration, $requestedEntityConfiguration);
    $relationParamProviderTree->appendCriteria($relationCriteria);

    return $relationParamProviderTree;
  }

  private function buildCriteria(
    ParamProviderTree $paramProviderTree,
    EntityConfiguration $entityConfiguration, // todo unused
    EntityConfiguration $requestedEntityConfiguration
  ) {
    $relationPrimaryKeys = [];
    foreach ($paramProviderTree->getResultManager()->getResult() as $result) {
      $relationPrimaryKeys[] = $result[$requestedEntityConfiguration->getEntityName()];
    }

    $pkKey = $requestedEntityConfiguration->getPkKey();

    // todo $field->field refactor! move to key || name
    return [
      $pkKey,
      'in',
      $relationPrimaryKeys
    ]; // todo "in" remove magic

  }
}

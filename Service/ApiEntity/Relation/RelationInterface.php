<?php

namespace Vadik4646\EntityApiBundle\Service\ApiEntity\Relation;

use Vadik4646\EntityApiBundle\Service\ApiEntity\EntityConfiguration;
use Vadik4646\EntityApiBundle\Service\ApiEntity\ParamProviderTree;

interface RelationInterface
{
  const MANY_TO_ONE  = 'ManyToOne';
  const ONE_TO_MANY  = 'OneToMany';
  const MANY_TO_MANY = 'ManyToMany';
  const ONE_TO_ONE   = 'OneToOne';

  /**
   * @param ParamProviderTree   $paramProviderTree
   * @param EntityConfiguration $entityConfiguration
   * @param EntityConfiguration $requestedEntityConfiguration
   * @param string              $requestedRelation
   * @return mixed
   */
  public function configureParams(
    ParamProviderTree $paramProviderTree,
    EntityConfiguration $entityConfiguration,
    EntityConfiguration $requestedEntityConfiguration,
    $requestedRelation
  );
}

<?php

namespace Vadik4646\EntityApiBundle\Service\ApiEntity;

use Doctrine\ORM\EntityManager;

class DataProvider
{
  private $entityManager;
  private $dqlBuilder;

  public function __construct(EntityManager $entityManager, DqlBuilder $dqlBuilder)
  {
    $this->entityManager = $entityManager;
    $this->dqlBuilder = $dqlBuilder;
  }

  /**
   * @param ParamProviderTree   $paramProviderTree
   * @param EntityConfiguration $entityConfiguration
   * @return ResultManager
   */
  public function getData(ParamProviderTree $paramProviderTree, EntityConfiguration $entityConfiguration)
  {
    $queryBuilder = $this->entityManager->createQueryBuilder();
    $entityAlias = $entityConfiguration->getEntityAlias();

    $queryBuilder->from($entityConfiguration->getEntityClass(), $entityAlias);
    $this->dqlBuilder->buildRelation($entityConfiguration, $paramProviderTree, $queryBuilder);

    $limit = null;
    $offset = null; // todo add default max limitation for limit
    if ($paramProviderTree->hasCriteria()) {
      $criteria = $this->dqlBuilder->buildCriteria(
        $queryBuilder,
        $entityConfiguration,
        $paramProviderTree->getCriteria()
      ); // todo prepared queries
      $queryBuilder->where($criteria);
    }

    if ($paramProviderTree->hasPk()) {
      $this->dqlBuilder->buildPkKeyCondition($queryBuilder, $entityConfiguration, $paramProviderTree);
      return new ResultManager($queryBuilder->getQuery()->getSingleResult());
    }

    if ($paramProviderTree->hasOrder()) { // todo
      $order = $paramProviderTree->getOrder();
    }

    if ($paramProviderTree->hasLimit()) {
      $limit = $paramProviderTree->getLimit();
    }

    if ($paramProviderTree->hasOffset()) {
      $offset = $paramProviderTree->getOffset();
    }

    return new ResultManager($queryBuilder->getQuery()->getResult());
  }
}

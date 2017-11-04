<?php

namespace Vadik4646\EntityApiBundle\Service\ApiEntity;

use Doctrine\ORM\EntityManager;

class DataProvider
{
  private $entityManager;
  private $paramProviderTree;
  private $entityConfiguration;
  private $dqlBuilder;

  public function __construct(EntityManager $entityManager, DqlBuilder $dqlBuilder)
  {
    $this->entityManager = $entityManager;
    $this->dqlBuilder = $dqlBuilder;
  }

  /**
   * @param ParamProviderTree $paramProviderTree
   * @param EntityConfiguration $entityConfiguration
   * @return ResultManager
   */
  public function getData(ParamProviderTree $paramProviderTree, EntityConfiguration $entityConfiguration)
  {
    $this->paramProviderTree = $paramProviderTree;
    $this->entityConfiguration = $entityConfiguration;

    $queryBuilder = $this->entityManager->createQueryBuilder();
    $tableAlias = $entityConfiguration->getTableAlias();

    $queryBuilder->select($this->dqlBuilder->buildSelect($entityConfiguration, $tableAlias));
    $queryBuilder->from($entityConfiguration->getEntityClass(), $tableAlias);

    $limit = null;
    $offset = null; // todo add default max limitation for limit

    if ($paramProviderTree->hasCriteria()) {
      $criteriaParams = $paramProviderTree->getCriteria();
      $criteria = $this->dqlBuilder->buildCriteria(
        $queryBuilder,
        $entityConfiguration,
        $criteriaParams
      ); // todo prepared queries
      $queryBuilder->where($criteria);
    }

    if ($paramProviderTree->hasPk()) {
      $queryBuilder->where(
        $tableAlias . '.' . $queryBuilder->expr()->eq($entityConfiguration->getPkKey(), $paramProviderTree->getPk())
      );

      return new ResultManager($queryBuilder->getQuery()->getArrayResult());
    }

    if ($paramProviderTree->hasOrder()) {
      $order = $paramProviderTree->getOrder();
    }

    if ($paramProviderTree->hasLimit()) {
      $limit = $paramProviderTree->getLimit();
    }

    if ($paramProviderTree->hasOffset()) {
      $offset = $paramProviderTree->getOffset();
    }

    return new ResultManager($queryBuilder->getQuery()->getArrayResult());
  }
}

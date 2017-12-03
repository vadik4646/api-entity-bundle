<?php

namespace Vadik4646\EntityApiBundle\Service\ApiEntity;

use Doctrine\Common\Collections\Expr\Expression;
use Doctrine\ORM\QueryBuilder;
use Vadik4646\EntityApiBundle\Annotations\Field;
use Vadik4646\EntityApiBundle\Annotations\ManyToMany;
use Vadik4646\EntityApiBundle\Annotations\ManyToOne;
use Vadik4646\EntityApiBundle\Annotations\OneToOne;
use Vadik4646\EntityApiBundle\Utils\RelationField;

class DqlBuilder
{
  /** @var $queryBuilder QueryBuilder */
  private $queryBuilder;
  /** @var $entityConfiguration EntityConfiguration */
  private $entityConfiguration;
  private static $defaultCondition = 'and';
  private static $conditionMapping = [
    'or'  => 'orX',
    'and' => 'andX'
  ];

  private static $operationMapping = [
    '='         => 'eq',
    '!='        => 'neq',
    '<'         => 'lt',
    '<='        => 'lte',
    '>'         => 'gt',
    '>='        => 'gte',
    'isNull'    => 'isNull',
    'isNotNull' => 'isNotNull',
    'in'        => 'in',
    'notIn'     => 'notIn',
    'like'      => 'like',
    'notLike'   => 'notLike',
    'between'   => 'between' // todo this isn't working right now. see L74
  ];

  /**
   * @param QueryBuilder        $queryBuilder
   * @param EntityConfiguration $entityConfiguration
   * @param array               $criteriaParams
   * @return Expression[]
   */
  public function buildCriteria(QueryBuilder $queryBuilder, EntityConfiguration $entityConfiguration, $criteriaParams)
  {
    $this->entityConfiguration = $entityConfiguration;
    $this->queryBuilder = $queryBuilder;

    return $this->buildCondition($queryBuilder, $entityConfiguration, $criteriaParams);
  }

  /**
   * @param EntityConfiguration $entityConfiguration
   * @param ParamProviderTree   $paramProviderTree
   * @param QueryBuilder        $queryBuilder
   */
  public function buildRelation(
    EntityConfiguration $entityConfiguration,
    ParamProviderTree $paramProviderTree,
    QueryBuilder $queryBuilder
  ) {
    $entityAlias = $entityConfiguration->getEntityAlias();
    $queryBuilder->addSelect($entityAlias);
    foreach ($entityConfiguration->getRelationFields() as $field) {
      if ($paramProviderTree->hasRelation($field->getName())) {
        $relationEntityConfiguration = $entityConfiguration->create($field->getEntity());
        $relationParamProviderTree = $paramProviderTree->createFromRelation($field->getName(), $field->getEntity());
        $queryBuilder->leftJoin(
          $entityAlias . '.' . $field->getName(),
          $relationEntityConfiguration->getEntityAlias(),
          'WITH'
        );

        $this->buildRelation($relationEntityConfiguration, $relationParamProviderTree, $queryBuilder);
      }
    }
  }

  /**
   * @param QueryBuilder        $queryBuilder
   * @param EntityConfiguration $entityConfiguration
   * @param ParamProviderTree   $paramProviderTree
   */
  public function buildPkKeyCondition(
    QueryBuilder $queryBuilder,
    EntityConfiguration $entityConfiguration,
    ParamProviderTree $paramProviderTree
  ) {
    $entityAlias = $entityConfiguration->getEntityAlias();
    $pkKey = $entityConfiguration->getPkKey();
    $pkValue = $paramProviderTree->getPk();
    $pkCondition = $entityAlias . '.' . $queryBuilder->expr()->eq($pkKey, $pkValue);
    $queryBuilder->where($pkCondition);
  }

  /**
   * @param QueryBuilder        $queryBuilder
   * @param EntityConfiguration $entityConfiguration
   * @param array               $criteriaParams
   * @return array
   */
  private function handle(QueryBuilder $queryBuilder, EntityConfiguration $entityConfiguration, $criteriaParams)
  {
    if (count($criteriaParams) === 3 && !is_array(current($criteriaParams))) {
      return [$this->buildSingleCondition($queryBuilder, $entityConfiguration, $criteriaParams)];
    }

    $criteria = [];
    foreach ($criteriaParams as $criteriaParamKey => $criteriaParam) {
      if (array_key_exists($criteriaParamKey, self::$conditionMapping)) {
        $criteria[] = $this->buildCondition($queryBuilder, $entityConfiguration, $criteriaParam, $criteriaParamKey);
      } else {
        $criteria[] = $this->buildCondition(
          $queryBuilder,
          $entityConfiguration,
          $criteriaParam,
          self::$defaultCondition
        );
      }
    }

    return $criteria;
  }

  /**
   * @param QueryBuilder        $queryBuilder
   * @param EntityConfiguration $entityConfiguration
   * @param array               $criteriaParams
   * @param string              $condition
   * @return Expression[]
   */
  private function buildCondition(
    QueryBuilder $queryBuilder,
    EntityConfiguration $entityConfiguration,
    $criteriaParams,
    $condition = null
  ) {
    $condition = $condition ?: self::$defaultCondition;
    $params = $this->handle($queryBuilder, $entityConfiguration, $criteriaParams);
    $currentCondition = self::$conditionMapping[$condition];

    return call_user_func_array(
      [$queryBuilder->expr(), $currentCondition],
      $params
    );
  }

  /**
   * @param QueryBuilder        $queryBuilder
   * @param EntityConfiguration $entityConfiguration
   * @param array               $criteriaParams
   * @return mixed|null
   */
  private function buildSingleCondition(
    QueryBuilder $queryBuilder,
    EntityConfiguration $entityConfiguration,
    $criteriaParams
  ) {
    list($column, $operation, $value) = $criteriaParams;
    if (!array_key_exists($operation, self::$operationMapping)) {
      return null;
    }

    $entityAlias = $entityConfiguration->getEntityAlias();
    if ($relationField = $entityConfiguration->getRelationFieldByColumn($column)) {
      $column = 'IDENTITY(' . $entityAlias . '.' . $relationField->name . ')';
    } else {
      $column = $entityAlias . '.' . $column;
    }

    $currentOperation = self::$operationMapping[$operation];

    return call_user_func_array(
      [$queryBuilder->expr(), $currentOperation],
      [$column, $value]
    );
  }
}

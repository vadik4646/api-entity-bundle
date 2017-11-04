<?php

namespace Vadik4646\EntityApiBundle\Service\ApiEntity;

use Doctrine\Common\Collections\Expr\Expression;
use Doctrine\ORM\QueryBuilder;
use Vadik4646\EntityApiBundle\Annotations\ManyToOne;
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
   * @param string              $tableAlias
   * @return array
   */
  public function buildSelect(EntityConfiguration $entityConfiguration, $tableAlias)
  {
    $fieldsMap = [];
    foreach ($entityConfiguration->getFields() as $field) {
      if ($field instanceof ManyToOne) {
        $fieldsMap[] = 'IDENTITY(' . $tableAlias . '.' . $field->name . ') as ' . $field->name; // todo refactor
      } elseif (!$field instanceof RelationField) {
        $fieldsMap[] = $tableAlias . '.' . $field->name;
      }
    }

    return $fieldsMap;
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
   * @param                     $criteriaParams
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

    $tableAlias = $entityConfiguration->getTableAlias();
    if ($relationField = $entityConfiguration->getRelationFieldByColumn($column)) {
      $column = 'IDENTITY(' . $tableAlias . '.' . $relationField->name . ')';
    } else {
      $column = $tableAlias . '.' . $column;
    }

    $currentOperation = self::$operationMapping[$operation];

    return call_user_func_array(
      [$queryBuilder->expr(), $currentOperation],
      [$column, $value]
    );
  }
}

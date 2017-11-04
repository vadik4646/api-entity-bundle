<?php

namespace Vadik4646\EntityApiBundle\Service\ApiEntity;

class ParamProviderTree
{
  private $params;

  private static $RELATION = 'relation';
  private static $ENTITY = 'entity';
  private static $CRITERIA = 'criteria';
  private static $WITH = 'with';
  private static $ORDER = 'order';
  private static $LIMIT = 'limit';
  private static $OFFSET = 'offset';
  private static $PK = 'pk'; // todo move to ID from config for every entity

  /** @var $pkKey string */
  private $pkKey;

  /** @var $currentResultManager ResultManager */
  private $currentResultManager = null;

  public function __construct($params = null, $entity = null)
  {
    if (is_null($params)) {
      return;
    }
    if (is_string($params)) {
      $params = [self::$RELATION => $params];
    }

    $defaultParams = [
      self::$RELATION => null,
      self::$CRITERIA => [],
      self::$WITH     => [],
      self::$ORDER    => null,
      self::$LIMIT    => null,
      self::$OFFSET   => null,
      self::$PK       => null
    ];

    $params = array_merge($defaultParams, $params);
    $params[self::$WITH] = (array)$params[self::$WITH];
    $params[self::$ENTITY] = $entity;
    $this->params = $params;
  }

  /**
   * @return bool
   */
  public function hasCriteria()
  {
    return !empty($this->params[self::$CRITERIA]);
  }

  /**
   * @return array
   */
  public function getCriteria()
  {
    return $this->params[self::$CRITERIA];
  }

  /**
   * @return bool
   */
  public function hasPk()
  {
    return isset($this->params[self::$PK]);
  }

  /**
   * @return string|null
   */
  public function getPk()
  {
    return $this->params[self::$PK];
  }

  /**
   * @param string $key
   */
  public function setPkKey($key)
  {
    $this->pkKey = $key;
  }

  /**
   * @return bool
   */
  public function hasOrder()
  {
    return !empty($this->params[self::$ORDER]);
  }

  /**
   * @return string|null
   */
  public function getOrder()
  {
    return $this->params[self::$ORDER];
  }

  /**
   * @return bool
   */
  public function hasLimit()
  {
    return !empty($this->params[self::$LIMIT]);
  }

  /**
   * @return string|null
   */
  public function getLimit()
  {
    return $this->params[self::$LIMIT];
  }

  /**
   * @return bool
   */
  public function hasOffset()
  {
    return !empty($this->params[self::$OFFSET]);
  }

  /**
   * @return string|null
   */
  public function getOffset()
  {
    return $this->params[self::$OFFSET];
  }

  /**
   * @return array
   */
  public function getRelations()
  {
    $relations = (array)$this->params[self::$WITH];
    $result = [];
    foreach ($relations as $relation) {
      if (is_string($relation)) {
        $result[] = $relation;
      } elseif (is_array($relation) && array_key_exists(self::$RELATION, $relation)) {
        $result[] = $relation[self::$RELATION];
      }
    }

    return $result;
  }

  /**
   * @return string
   */
  public function getEntity()
  {
    return $this->params[self::$ENTITY];
  }

  /**
   * @param ResultManager $resultManager
   */
  public function setResultManager(ResultManager $resultManager)
  {
    $this->currentResultManager = $resultManager;
  }

  /**
   * @return ResultManager
   */
  public function getResultManager()
  {
    return $this->currentResultManager;
  }

  /**
   * @param string $relation
   * @param string $entity
   * @return ParamProviderTree
   */
  public function createFromRelation($relation, $entity)
  {
    $relationKey = array_search($relation, $this->params[self::$WITH]);

    return new self($this->params[self::$WITH][$relationKey], $entity);
  }

  /**
   * @param array $criteria
   */
  public function appendCriteria($criteria)
  {
    array_unshift($this->params[self::$CRITERIA], $criteria);
  }

  /**
   * @param array  $params
   * @param string $entity
   * @return ParamProviderTree
   */
  public function create($params, $entity)
  {
    return new self($params, $entity);
  }
}

<?php

namespace Vadik4646\EntityApiBundle\Service\ApiEntity;

use Vadik4646\EntityApiBundle\Annotations\ManyToMany;
use Vadik4646\EntityApiBundle\Annotations\ManyToOne;
use Vadik4646\EntityApiBundle\Annotations\OneToMany;
use Vadik4646\EntityApiBundle\Annotations\OneToOne;
use Vadik4646\EntityApiBundle\Utils\RelationField;

class Response
{
  private $resultManager;

  public function __construct(ResultManager $resultManager)
  {
    $this->resultManager = $resultManager;
  }

  /**
   * @return array
   */
  public function toArray()
  {
    $rawData = $this->getRawData();

    return $rawData ?: [];
  }

  /**
   * @return array|null
   */
  private function getRawData()
  {
    return $this->parseResultManager($this->resultManager);
  }

  /**
   * @param ResultManager $resultManager
   * @return array|null
   */
  public function parseResultManager(ResultManager $resultManager)
  {
    $firstResult = current($resultManager->getResult());
    if (!$firstResult) {
      return null;
    }

    $data = [];
    if ($resultManager->getParamProviderTree()->hasPk()) {
      $data = $this->extractData($firstResult, $resultManager); // todo refactor
    } else {
      foreach ($resultManager->getResult() as $entityRow) {
        $data[] = $this->extractData($entityRow, $resultManager);
      }
    }

    return $data;
  }

  /**
   * @param               $entityRow
   * @param ResultManager $resultManager
   * @return array
   */
  private function extractData($entityRow, ResultManager $resultManager)
  {
    $data = [];
    $relationData = $resultManager->getRelationData();
    $fields = $resultManager->getEntityConfiguration()->getFields();
    foreach ($fields as $field) {
      if ($field instanceof RelationField) {
        if (array_key_exists($field->name, $relationData)) {
          $relationResultManager = $relationData[$field->name];
          // todo HIGH PRIORITY field move to current relation name. HIGH PRIORITY
          $data[$field->name] = $this->handleRelation($entityRow, $resultManager, $relationResultManager);
        } else {
          if (array_key_exists($field->name, $entityRow)) {
            $data[$field->name] = $entityRow[$field->name];
          }
        }
      } else {
        $data[$field->name] = $entityRow[$field->name];
      }
    }

    return $data;
  }

  /**
   * @param array         $entityRow
   * @param ResultManager $resultManager
   * @param ResultManager $relationResultManager
   * @return array|null
   */
  private function handleRelation($entityRow, ResultManager $resultManager, ResultManager $relationResultManager)
  {
    $relationEntityConfiguration = $relationResultManager->getEntityConfiguration();
    $relationField = $resultManager->getEntityConfiguration()->getRelationField($relationEntityConfiguration);

    if ($relationField instanceof ManyToMany) {
      // todo
    } elseif ($relationField instanceof ManyToOne) {
      return $this->getManyToOneData($entityRow, $resultManager, $relationResultManager);
    } elseif ($relationField instanceof OneToMany) {
      return $this->getOneToManyData($entityRow, $resultManager, $relationResultManager);
    } elseif ($relationField instanceof OneToOne) {
      // todo
    }

    return null;
  }

  /**
   * @param array         $entityRow
   * @param ResultManager $resultManager
   * @param ResultManager $relationResultManager
   * @return array
   */
  private function getOneToManyData($entityRow, ResultManager $resultManager, ResultManager $relationResultManager)
  {
    $entityConfiguration = $resultManager->getEntityConfiguration();

    $relationEntityConfiguration = $relationResultManager->getEntityConfiguration();
    $field = $relationEntityConfiguration->getFieldByEntityName($entityConfiguration->getEntityName());
    $pkKey = $relationEntityConfiguration->getPkKey();

    $relationResult = $this->parseResultManager($relationResultManager);
    $result = [];
    foreach ($relationResult as $row) {
      $resultRow = $relationResultManager->findRowBy($pkKey, $row[$pkKey]);
      if ($resultRow && $resultRow[$field->entity] == $entityRow[$pkKey]) {
        $result[] = $row;
      }
    }

    return $result;
  }

  /**
   * @param array         $entityRow
   * @param ResultManager $resultManager
   * @param ResultManager $relationResultManager
   * @return array|null
   */
  private function getManyToOneData($entityRow, ResultManager $resultManager, ResultManager $relationResultManager)
  {
    $entityConfiguration = $resultManager->getEntityConfiguration();
    $relationEntityConfiguration = $relationResultManager->getEntityConfiguration();

    $field = $entityConfiguration->getFieldByEntityName($relationEntityConfiguration->getEntityName());

    $relationResult = $this->parseResultManager($relationResultManager);
    foreach ($relationResult as $row) {
      if ($entityRow[$field->name] == $row[$relationEntityConfiguration->getPkKey()]) {
        return $row;
      }
    }

    return null;
  }
}

<?php

namespace Vadik4646\EntityApiBundle\Service\ApiEntity\Relation;

class RelationFactory
{
  private static $relationHandlers = [
    RelationInterface::MANY_TO_ONE  => ManyToOne::class,
    RelationInterface::ONE_TO_MANY  => OneToMany::class,
    RelationInterface::MANY_TO_MANY => ManyToMany::class,
    RelationInterface::MANY_TO_MANY => ManyToMany::class,
  ];

  /**
   * @param $key
   * @return null|RelationInterface
   */
  public static function get($key)
  {
    return array_key_exists($key, self::$relationHandlers) ? new self::$relationHandlers[$key] : null;
  }
}

<?php
/**
 * Created by PhpStorm.
 * User: vtabaran
 * Date: 10/23/17
 * Time: 1:02 AM
 */

namespace Vadik4646\EntityApiBundle\Service\ApiEntity\Relation;

use Vadik4646\EntityApiBundle\Service\ApiEntity\EntityConfiguration;
use Vadik4646\EntityApiBundle\Service\ApiEntity\ParamProviderTree;

class ManyToMany
{
  public function configureParams(
    ParamProviderTree $paramProviderTree,
    EntityConfiguration $entityConfiguration,
    $requestedRelation
  ) {
    // todo implement
  }
}

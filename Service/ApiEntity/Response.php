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
    $this->resultManager->getResult();
  }
}

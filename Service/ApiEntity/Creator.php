<?php

namespace Vadik4646\EntityApiBundle\Service\ApiEntity;

use Symfony\Component\HttpFoundation\Request;

class Creator
{
  /**  @var ApiEntity */
  private $apiEntity;

  public function __construct(ApiEntity $apiEntity)
  {
    $this->apiEntity = $apiEntity;
  }

  /**
   * @param Request $request
   * @param string  $entity
   * @return Response
   */
  public function createFromJsonRequest(Request $request, $entity)
  {
    $jsonRequest = json_decode($request->getContent(), true);

    return $this->apiEntity->get($entity, $jsonRequest['method'], $jsonRequest['params']);
  }
}

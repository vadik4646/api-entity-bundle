<?php

namespace Vadik4646\EntityApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GeneralController extends Controller
{
  /**
   * @Route("/{path}", requirements={"path"=".+"})
   * @param Request $request
   * @param string  $path
   * @return Response
   */
  public function processAction(Request $request, $path)
  {// todo move to request listener
    $uriParts = explode('/', $path);
    $entityName = current($uriParts);

    $response = $this->get('entity_api.creator')
      ->createFromJsonRequest($request, $entityName)
      ->toArray();

    return new JsonResponse($response);
  }
}

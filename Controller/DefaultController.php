<?php

namespace Vadik4646\EntityApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('EntityApiBundle:Default:index.html.twig');
    }
}

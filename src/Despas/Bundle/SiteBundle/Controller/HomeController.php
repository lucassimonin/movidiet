<?php

namespace Despas\Bundle\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends Controller
{
    public function indexAction(Request $request, $locationId, $viewType, $layout = false, array $params = array())
    {
        $response = $this->get('ez_content')->viewLocation(
            $locationId,
            $viewType,
            $layout,
            $params
        );

        return $response;
    }
}

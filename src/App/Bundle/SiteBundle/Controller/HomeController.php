<?php

namespace App\Bundle\SiteBundle\Controller;

use App\Bundle\SiteBundle\Helper\CoreHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends Controller
{
    /** @var  CoreHelper */
    protected $coreHelper;

    public function indexAction(Request $request, $locationId, $viewType, $layout = false, array $params = array())
    {
        $this->coreHelper = $this->container->get('app.core_helper');
        $formuleItemContentTypeIdentifier = $this->container->getParameter('app.formule.content_type.identifier');
        $formulesLocationId = $this->container->getParameter('app.formules.locationid');
        $params['articles'] = $this->coreHelper->getLatestArticles();
        $params['formules'] = $this->coreHelper->getChildrenObject([$formuleItemContentTypeIdentifier], $formulesLocationId);
        $response = $this->get('ez_content')->viewLocation(
            $locationId,
            $viewType,
            $layout,
            $params
        );

        return $response;
    }
}

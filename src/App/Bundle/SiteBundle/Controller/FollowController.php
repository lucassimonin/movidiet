<?php

namespace App\Bundle\SiteBundle\Controller;

use App\Bundle\SiteBundle\Helper\CoreHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Bundle\SiteBundle\Entity\Contact;
use Symfony\Component\HttpFoundation\JsonResponse;

class FollowController extends Controller
{
    /** @var  CoreHelper */
    protected $coreHelper;

    /**
     * Homepage action
     * @param $locationId
     * @param $viewType
     * @param bool $layout
     * @param array $params
     * @return mixed
     */
    public function indexAction($locationId, $viewType, $layout = false, array $params = array())
    {

        $this->coreHelper = $this->container->get('app.core_helper');
        $params['home'] = $this->coreHelper->getContentHomepage();


        return $this->render( '@AppSite/follow/index.html.twig', array('params' => $params));
    }
}

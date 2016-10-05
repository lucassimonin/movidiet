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
        $galleryItemContentTypeIdentifier = $this->container->getParameter('app.item_gallery.content_type.identifier');
        $galleryLocationId = $this->container->getParameter('app.gallery.locationid');
        $params['news'] = $this->coreHelper->getLatestNews();
        $params['gallery_items'] = $this->coreHelper->getChildrenObject([$galleryItemContentTypeIdentifier], $galleryLocationId);
        $response = $this->get('ez_content')->viewLocation(
            $locationId,
            $viewType,
            $layout,
            $params
        );

        return $response;
    }
}

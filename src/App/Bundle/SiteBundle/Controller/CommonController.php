<?php


namespace Despas\Bundle\SiteBundle\Controller;

use eZ\Bundle\EzPublishCoreBundle\Controller;
use Symfony\Component\HttpFoundation\Response;
use eZ\Publish\Core\MVC\Symfony\Routing\RouteReference;
use Symfony\Component\HttpFoundation\Request;

/**
 * CommonController Class.
 *
 * @author simoninl
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassLength)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class CommonController extends Controller
{

    /**
     * languagesAction
     *
     * @param RouteReference $routeRef
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function languagesAction(Request $request, RouteReference $routeRef )
    {
        // get cuurent eZ language
        $currentSFLanguage = $request->get( '_locale');
        $currentEzLanguage = array_search(
            $currentSFLanguage ,
            $this->container->getParameter( 'ezpublish.locale.conversion_map' )
        );

        return $this->render( '@DespasSite/content/parts/languages.html.twig',
            array('currentLanguage' => $currentEzLanguage, 'routeRef' => $routeRef)
        );
    }

}

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
        $formuleItemContentTypeIdentifier = $this->container->getParameter('app.formule.content_type.identifier');
        $formulesLocationId = $this->container->getParameter('app.formules.locationid');
        $articles = array();
        // Sport
        $article = $this->coreHelper->getLatestArticles($this->container->getParameter('app.article.category.sport'));
        if( $article != null) {
            array_push($articles, $article);
        }
        // Diet
        $article = $this->coreHelper->getLatestArticles($this->container->getParameter('app.article.category.diet'));
        if( $article != null) {
            if($articles != null) {
                array_push($articles, $article);
            } else {
                $articles = $article;
            }
        }
        //Recipe
        $article = $this->coreHelper->getLatestArticles($this->container->getParameter('app.article.category.recipe'));
        if( $article != null) {
            if($articles != null) {
                array_push($articles, $article);
            } else {
                $articles = $article;
            }
        }
        $params['articles'] = $articles;
        $params['blogLocationId'] = $this->container->getParameter('app.blog.locationid');
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

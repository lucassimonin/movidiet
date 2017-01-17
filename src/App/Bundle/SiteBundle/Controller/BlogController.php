<?php

namespace App\Bundle\SiteBundle\Controller;

use App\Bundle\SiteBundle\Helper\CoreHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BlogController extends Controller
{
    /** @var  CoreHelper */
    protected $coreHelper;

    /**
     * Index blog
     * @param $locationId
     * @param $viewType
     * @param bool $layout
     * @param array $params
     * @return mixed
     */
    public function indexAction($locationId, $viewType, $layout = false, array $params = array())
    {
        $this->coreHelper = $this->container->get('app.core_helper');
        $blogLimitArticle = $this->container->getParameter('app.blog.article.limit');
        $params['home'] = $this->coreHelper->getContentHomepage();
        $params['blogLocationId'] = $this->container->getParameter('app.blog.locationid');
        // Sport
        $params['sport_articles'] = $this->coreHelper->getLatestArticles($this->container->getParameter('app.article.category.sport'), $blogLimitArticle);
        // Diet
        $params['diet_articles'] = $this->coreHelper->getLatestArticles($this->container->getParameter('app.article.category.diet'), $blogLimitArticle);
        //Recipe
        $params['recipe_articles'] = $this->coreHelper->getLatestArticles($this->container->getParameter('app.article.category.recipe'), $blogLimitArticle);

        $response = $this->get('ez_content')->viewLocation(
            $locationId,
            $viewType,
            $layout,
            $params
        );

        $response->headers->set('X-Location-Id', $locationId);
        $response->setEtag(md5(json_encode($params)));
        $response->setPublic();
        $response->setSharedMaxAge($this->container->getParameter('app.cache.high.ttl'));

        return $response;
    }

    /**
     * Show article
     * @param $locationId
     * @param $viewType
     * @param bool $layout
     * @param array $params
     * @return mixed
     */
    public function showAction($locationId, $viewType, $layout = false, array $params = array())
    {
        $this->coreHelper = $this->container->get('app.core_helper');
        $params['blogLocationId'] = $this->container->getParameter('app.blog.locationid');
        $params['home'] = $this->coreHelper->getContentHomepage();

        $response = $this->get('ez_content')->viewLocation(
            $locationId,
            $viewType,
            $layout,
            $params
        );

        $response->headers->set('X-Location-Id', $locationId);
        $response->setEtag(md5(json_encode($params)));
        $response->setPublic();
        $response->setSharedMaxAge($this->container->getParameter('app.cache.high.ttl'));

        return $response;
    }
}

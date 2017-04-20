<?php

namespace App\Bundle\SiteBundle\Controller;

use App\Bundle\SiteBundle\Helper\CoreHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use eZ\Publish\Core\MVC\Symfony\View\View;

class BlogController extends Controller
{
    /** @var  CoreHelper */
    protected $coreHelper;

    /**
     * Index blog
     * @param View $view
     * @return View
     */
    public function indexAction(View $view)
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

        $response = new Response();

        $response->headers->set('X-Location-Id', $view->getLocation()->id);
        $response->setEtag(md5(json_encode($params)));
        $response->setPublic();
        $response->setSharedMaxAge($this->container->getParameter('app.cache.high.ttl'));

        $view->setResponse($response);

        $view->addParameters([
            'params' => $params,
        ]);

        return $view;
    }

    /**
     * Show article
     * @param View $view
     * @return View
     */
    public function showAction(View $view)
    {
        $this->coreHelper = $this->container->get('app.core_helper');
        $params['blogLocationId'] = $this->container->getParameter('app.blog.locationid');
        $params['home'] = $this->coreHelper->getContentHomepage();

        $response = new Response();

        $response->headers->set('X-Location-Id', $view->getLocation()->id);
        $response->setEtag(md5(json_encode($params)));
        $response->setPublic();
        $response->setSharedMaxAge($this->container->getParameter('app.cache.high.ttl'));

        $view->setResponse($response);

        $view->addParameters([
            'params' => $params,
        ]);

        return $view;
    }
}

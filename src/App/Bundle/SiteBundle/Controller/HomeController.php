<?php

namespace App\Bundle\SiteBundle\Controller;

use App\Bundle\SiteBundle\Helper\CoreHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use eZ\Publish\Core\MVC\Symfony\View\View;
use App\Bundle\SiteBundle\Entity\Contact;
use Symfony\Component\HttpFoundation\JsonResponse;

class HomeController extends Controller
{
    /** @var  CoreHelper */
    protected $coreHelper;

    /**
     * Homepage action
     * @param View $view
     * @return View
     */
    public function indexAction(View $view)
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

        $contact = new Contact();
        $form = $this->createForm($this->get('app.form.type.contact'), $contact, array());
        $response = new Response();
        $response->headers->set('X-Location-Id', $view->getLocation()->id);
        $response->setPublic();
        $response->setSharedMaxAge($this->container->getParameter('app.cache.high.ttl'));

        $view->setResponse($response);

        $view->addParameters([
            'form' => $form->createView(),
            'articles' => $articles,
            'blogLocationId' => $this->container->getParameter('app.blog.locationid'),
            'formules' => $this->coreHelper->getChildrenObject([$formuleItemContentTypeIdentifier], $formulesLocationId)
        ]);

        return $view;
    }

    public function contactFormAction(Request $request)
    {
        $repository = $this->container->get( 'ezpublish.api.repository' );
        $contentService = $repository->getContentService();
        $locationService = $repository->getLocationService();
        $contentTypeService = $repository->getContentTypeService();
        $this->coreHelper = $this->container->get('app.core_helper');
        $result = [
            'error_code' => 0
        ];

        $contact = new Contact();
        $form = $this->createForm($this->get('app.form.type.contact'), $contact, array());
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                // Getting content type identifier for license
                $contactContentTypeIdentifier = $this->container->getParameter('app.contact.content_type.identifier');
                $formData = $form->getData();

                $contentType = $contentTypeService->loadContentTypeByIdentifier( $contactContentTypeIdentifier );
                $contentCreateStruct = $contentService->newContentCreateStruct( $contentType, 'fre-FR' );

                $contentCreateStruct->setField('name', $formData->name);
                $contentCreateStruct->setField('message', $formData->message);
                $contentCreateStruct->setField('email', $formData->email);

                $contentCreateStruct->setField('subject', $this->coreHelper->transformFieldToSelection($formData->subject));

                $locationCreateStruct = $locationService->newLocationCreateStruct( $this->container->getParameter('app.contacts.locationid') );

                $draft = $contentService->createContent( $contentCreateStruct, array( $locationCreateStruct ) );
                $contentService->publishVersion( $draft->versionInfo );
            }
        }

        return new JsonResponse($result);

    }

    /**
     * Get condition general
     */
    public function conditionsGeneralesAction()
    {
        return $this->render('@AppSite/content/full/conditions.html.twig');
    }
}

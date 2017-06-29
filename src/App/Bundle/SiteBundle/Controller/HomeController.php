<?php

namespace App\Bundle\SiteBundle\Controller;

use App\Bundle\SiteBundle\Form\Type\ContactType;
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
    public function indexAction(View $view) : View
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $response = new Response();
        $response->headers->set('X-Location-Id', $view->getLocation()->id);
        $response->setPublic();
        $response->setSharedMaxAge($this->container->getParameter('app.cache.high.ttl'));
        $view->setResponse($response);
        $view->addParameters([
            'form' => $form->createView(),
            'blogLocationId' => $this->container->getParameter('app.blog.locationid'),
        ]);

        return $view;
    }

    /**
     * BlogAction
     * @return Response
     */
    public function blogAction() : Response
    {
        $this->coreHelper = $this->container->get('app.core_helper');
        $articles = [];
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

        return $this->render(
            '@AppSite/content/parts/blog.html.twig',
            [
                'articles' => $articles,
                'blogLocationId' => $this->container->getParameter('app.blog.locationid')
            ]
        );
    }

    /**
     * Formule action
     * @return Response
     */
    public function formuleAction() : Response
    {
        $this->coreHelper = $this->container->get('app.core_helper');
        $formuleItemContentTypeIdentifier = $this->container->getParameter('app.formule.content_type.identifier');
        $formulesLocationId = $this->container->getParameter('app.formules.locationid');

        return $this->render(
            '@AppSite/content/parts/formules.html.twig',
            array('formules' => $this->coreHelper->getChildrenObject([$formuleItemContentTypeIdentifier], $formulesLocationId))
        );
    }

    /**
     * Contact form action
     * @param Request $request
     * @return JsonResponse
     */
    public function contactFormAction(Request $request) : JsonResponse
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
        $form = $this->createForm(ContactType::class, $contact);
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
                $draft = $contentService->createContent( $contentCreateStruct, [$locationCreateStruct] );
                $contentService->publishVersion( $draft->versionInfo );
            } else {
                $result = [
                    'error_code' => 1,
                    'msg' => 'not valid form'
                ];
            }
        } else {
            $result = [
                'error_code' => 1,
                'msg' => 'not POST method'
            ];
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

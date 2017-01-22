<?php

namespace App\Bundle\SiteBundle\Controller;

use App\Bundle\SiteBundle\Helper\CoreHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

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

        $params = $this->getUserInformation();

        if($params['error']) {
            return $params['response'];
        }

        $this->coreHelper = $this->container->get('app.core_helper');
        $usersLocationId = $this->container->getParameter('app.users.locationid');
        $usersContentTypeId = $this->container->getParameter('app.users.content_type.identifier');
        $params['users'] = $this->coreHelper->getChildrenObject([$usersContentTypeId], $usersLocationId);

        return $this->render( '@AppSite/follow/index.html.twig', array('params' => $params));
    }

    public function visitAction($userId)
    {
        $params = $this->getUserInformation();
        $this->coreHelper = $this->container->get('app.core_helper');
        $params['user'] = $this->coreHelper->getContentById($userId);

        return $this->render( '@AppSite/follow/visit.html.twig', array('params' => $params));
    }

    public function profilAction($userId)
    {
        $params = $this->getUserInformation();
        $this->coreHelper = $this->container->get('app.core_helper');
        $params['user'] = $this->coreHelper->getContentById($userId);

        return $this->render( '@AppSite/follow/profil.html.twig', array('params' => $params));
    }

    public function changePasswordAction($userId)
    {
        $params = $this->getUserInformation();
        $this->coreHelper = $this->container->get('app.core_helper');
        $params['user'] = $this->coreHelper->getContentById($userId);

        return $this->render( '@AppSite/follow/changepassword.html.twig', array('params' => $params));
    }

    public function rationsAction($userId)
    {
        $params = $this->getUserInformation();
        $this->coreHelper = $this->container->get('app.core_helper');
        $params['user'] = $this->coreHelper->getContentById($userId);

        return $this->render( '@AppSite/follow/rations.html.twig', array('params' => $params));
    }

    public function trainingAction($userId)
    {
        $params = $this->getUserInformation();
        $this->coreHelper = $this->container->get('app.core_helper');
        $params['user'] = $this->coreHelper->getContentById($userId);


        return $this->render( '@AppSite/follow/training.html.twig', array('params' => $params));
    }

    /**
     * Get user information
     * @return array
     */
    public function getUserInformation()
    {
        $securityContext = $this->container->get('security.authorization_checker');
        if (!$securityContext->isGranted('IS_AUTHENTICATED_FULLY')) {
            return array('error' => true, 'response' => new RedirectResponse($this->container->get('router')->generate('login')));
        }
        $user = $this->get('security.context')->getToken()->getUser()->getAPIUser();
        $params['error'] = false;
        $params['admin'] = $user->getFieldValue('administrateur')->bool;
        $params['contact'] = $this->container->getParameter('app.email.contact');

        return $params;
    }
}

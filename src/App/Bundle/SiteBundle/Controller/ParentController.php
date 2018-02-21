<?php

namespace App\Bundle\SiteBundle\Controller;

use App\Bundle\SiteBundle\Helper\CoreHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ParentController extends Controller
{
    /** @var  CoreHelper */
    protected $coreHelper;

    /**
     * Get user information
     * @return array
     */
    public function getUserInformation()
    {
        $securityContext = $this->container->get('security.authorization_checker');
        if (!$securityContext->isGranted('IS_AUTHENTICATED_FULLY')) {
            return ['error' => true, 'response' => new RedirectResponse($this->container->get('router')->generate('login'))];
        }
        $user = $this->get('security.token_storage')->getToken()->getUser()->getAPIUser();
        $params['error'] = false;
        $params['user'] = $user;
        $params['admin'] = $user->getFieldValue('administrateur')->bool;
        $params['contact'] = $this->container->getParameter('app.email.contact');

        return $params;
    }

}

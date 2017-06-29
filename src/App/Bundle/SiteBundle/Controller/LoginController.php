<?php

namespace App\Bundle\SiteBundle\Controller;

use App\Bundle\SiteBundle\Helper\CoreHelper;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class LoginController
{
    /** @var  CoreHelper */
    protected $coreHelper;

    /**
     * @var \Symfony\Component\Templating\EngineInterface
     */
    protected $templateEngine;

    /**
     * @var \eZ\Publish\Core\MVC\ConfigResolverInterface
     */
    protected $configResolver;

    /**
     * @var \Symfony\Component\Security\Http\Authentication\AuthenticationUtils
     */
    protected $authenticationUtils;

    /** @var \Symfony\Component\DependencyInjection\Container */
    protected $container;

    public function __construct(EngineInterface $templateEngine, ConfigResolverInterface $configResolver, AuthenticationUtils $authenticationUtils, $container)
    {
        $this->templateEngine = $templateEngine;
        $this->configResolver = $configResolver;
        $this->authenticationUtils = $authenticationUtils;
        $this->container = $container;
    }

    /**
     * Login action
     * @return RedirectResponse|Response
     */
    public function loginAction()
    {
        $securityContext = $this->container->get('security.authorization_checker');
        if ($securityContext->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new RedirectResponse($this->container->get('router')->generate('follow-index'));

        }
        $this->coreHelper = $this->container->get('app.core_helper');
        $params['home'] = $this->coreHelper->getContentHomepage();
        return new Response(
            $this->templateEngine->render(
                $this->configResolver->getParameter('security.login_template'),
                [
                    'last_username' => $this->authenticationUtils->getLastUsername(),
                    'error' => $this->authenticationUtils->getLastAuthenticationError(),
                    'layout' => $this->configResolver->getParameter('security.base_layout'),
                    'params' => $params
                ]
            )
        );
    }
}

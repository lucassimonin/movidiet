<?php

namespace App\Bundle\SiteBundle\Controller;

use App\Bundle\SiteBundle\Entity\User;

class FollowController extends ParentController
{
    const ADMIN_ID = 1;


    /**
     * Homepage action
     * @return mixed
     */
    public function indexAction()
    {

        $params = $this->getUserInformation();

        if ($params['error']) {
            return $params['response'];
        } else if (!$params['admin']) {
            return $this->redirect($this->generateUrl('follow-visit-patient'));
        }

        unset($params['user']);

        $this->coreHelper = $this->container->get('app.core_helper');
        $usersLocationId = $this->container->getParameter('app.users.locationid');
        $usersContentTypeId = $this->container->getParameter('app.users.content_type.identifier');
        $params['users'] = $this->coreHelper->getChildrenObject([$usersContentTypeId], $usersLocationId);

        return $this->render( '@AppSite/follow/index.html.twig', ['params' => $params]);
    }

    /**
     * Ration action
     * @param null|string $userId
     * @return mixed|\Symfony\Component\HttpFoundation\Response
     */
    public function rationsAction($userId = null)
    {
        $params = $this->getUserInformation();
        if($params['error']) {
            return $params['response'];
        }
        $this->coreHelper = $this->container->get('app.core_helper');
        if ($userId != null && $params['admin']) {
            $params['user'] = $this->coreHelper->getContentById($userId);
        }
        $user = new User();
        $userHelper  = $this->container->get('app.user_helper');
        $userHelper->loadUserObjectByEzApiUser($user, $params['user']->versionInfo->contentInfo->id);
        $params['colorFatMass'] = $userHelper->getColorFatMass($user);

        return $this->render( '@AppSite/follow/rations.html.twig', ['params' => $params]);
    }
}

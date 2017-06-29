<?php

namespace App\Bundle\SiteBundle\Controller;

use App\Bundle\SiteBundle\Entity\User;
use App\Bundle\SiteBundle\Entity\Visit;
use App\Bundle\SiteBundle\Form\Type\VisitType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;


class VisitController extends ParentController
{

    /**
     * Visit patient admin or patient view
     * @param null|string $userId
     * @return mixed|\Symfony\Component\HttpFoundation\Response
     */
    public function visitAction($userId = null)
    {
        $params = $this->getUserInformation();
        if($params['error']) {
            return $params['response'];
        }
        $form = null;
        if($params['admin']) {
            $visit = new Visit();
            $visit->setUserId($userId);
            $form = $this->createForm(VisitType::class, $visit);
            $form = $form->createView();
        }
        $this->coreHelper = $this->container->get('app.core_helper');
        if ($userId != null) {
            $params['user'] = $this->coreHelper->getContentById($userId);
        }
        $user = new User();
        $userHelper  = $this->container->get('app.user_helper');
        $userHelper->loadUserObjectByEzApiUser($user, $params['user']->versionInfo->contentInfo->id);
        $params['colorFatMass'] = $userHelper->getColorFatMass($user);


        $em = $this->getDoctrine()->getManager();
        $visits = $em->getRepository('AppSiteBundle:Visit')->findBy(['userId' => $params['user']->versionInfo->contentInfo->id], ['date' => 'ASC']);
        $params['visits'] = [];
        if(count($visits) > 0 ) {
            foreach($visits as $visit) {
                $visitArray = $visit->_toArray();
                $params['visits'][] = ['date' => $visit->getDate()->format('d-m-Y'),
                    'weight' => floatval($visitArray['weight']),
                    'fatMass' => floatval($visitArray['fatMass']),
                    'arm' => floatval($visitArray['arm']),
                    'thigh' => floatval($visitArray['thigh']),
                    'chest' => floatval($visitArray['chest']),
                    'hip' => floatval($visitArray['hip']),
                    'size' => floatval($visitArray['size']),
                    'id' => $visit->getId()
                ];

            }
            $params['visits_json'] = json_encode($params['visits']);
        }

        return $this->render( '@AppSite/follow/visit.html.twig', ['params' => $params, 'form' => $form]);
    }

    /**
     * Add visit action
     * @param Request $request
     * @return JsonResponse
     */
    public function addVisitAction(Request $request)
    {
        $this->coreHelper = $this->container->get('app.core_helper');
        $visit = new Visit();
        $result = [
            'error_code' => 0
        ];

        $form = $this->createForm(VisitType::class, $visit);
        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            $user = null;
            try {
                $user = $this->coreHelper->getContentById($visit->getUserId());
            } catch (\Exception $e) {
                $result['error_code'] = 1;
                $result['message'] = $this->get('translator')->trans('app.user_not_exist');
            }

            $userAdd = new User();
            $userAdd->setWeight($visit->getWeight());
            $userAdd->setFatMass($visit->getFatMass());
            $userAdd->setId($visit->getUserId());
            $userHelper = $this->get('app.user_helper');
            $userHelper->update($userAdd, true);
            $visit->setVisitJson(base64_encode($visit->_toJson()));
            $em = $this->getDoctrine()->getManager();
            $em->persist($visit);
            $em->flush();
            $userHelper->loadUserObjectByEzApiUser($userAdd, $visit->getUserId());
            $result['data'] = ['date' => $visit->getDate()->format('d-m-Y'),
                'weight' => $visit->getWeight(),
                'massG' => $visit->getFatMass(),
                'colorFatMass' => $userHelper->getColorFatMass($userAdd),
                'arm' => $visit->getArm(),
                'chest' => $visit->getChest(),
                'hip' => $visit->getHip(),
                'thigh' => $visit->getThigh(),
                'size' => $visit->getSize(),
                'path' => $this->generateUrl('remove-visit', ['id' => $visit->getId(), 'userId' => $visit->getUserId()])
            ];

            return new JsonResponse($result);
        }
    }

    /**
     * Remove visit
     * @param $id
     * @param $userId
     * @return mixed|RedirectResponse
     */
    public function removeVisitAction($id, $userId)
    {
        $params = $this->getUserInformation();
        if($params['error']) {
            return $params['response'];
        }
        if($params['admin']) {
            $em = $this->getDoctrine()->getManager();
            /** @var Visit $visit */
            $visit = $em->getRepository('AppSiteBundle:Visit')->find($id);
            if($visit == null || $visit != null && intval($visit->getUserId()) != $userId) {
                $this->get('session')->getFlashBag()->add(
                    'notice',
                    [
                        'alert' => 'danger',
                        'message' => $this->get('translator')->trans('app.visit_not_exist')
                    ]
                );

                return $this->redirectToRoute('follow-visit', ['userId' => $userId]);
            }
            $em->remove($visit);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'notice',
                [
                    'alert' => 'success',
                    'message' => $this->get('translator')->trans('app.visit_remove.success')
                ]
            );
        }

        return $this->redirectToRoute('follow-visit', ['userId' => $userId]);
    }

}

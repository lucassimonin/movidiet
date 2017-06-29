<?php

namespace App\Bundle\SiteBundle\Controller;

use App\Bundle\SiteBundle\Entity\Training;
use App\Bundle\SiteBundle\Entity\User;
use App\Bundle\SiteBundle\Form\Type\TrainingType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;


class TrainingController extends ParentController
{
    /**
     * Training action
     * @param null $userId
     * @return mixed|\Symfony\Component\HttpFoundation\Response
     */
    public function trainingAction($userId = null)
    {
        $params = $this->getUserInformation();
        if($params['error']) {
            return $params['response'];
        }
        $this->coreHelper = $this->container->get('app.core_helper');
        if ($userId != null) {
            $params['user'] = $this->coreHelper->getContentById($userId);
        }
        $form = null;
        if($params['admin']) {
            $training = new Training();
            $training->setUserId($userId);
            $form = $this->createForm(TrainingType::class, $training);
            $form = $form->createView();
        }

        $user = new User();
        $userHelper  = $this->container->get('app.user_helper');
        $userHelper->loadUserObjectByEzApiUser($user, $params['user']->versionInfo->contentInfo->id);
        $params['colorFatMass'] = $userHelper->getColorFatMass($user);
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository('AppSiteBundle:Training')
            ->createQueryBuilder('t')
            ->where('t.userId = :user')
            ->setParameter('user', $params['user']->versionInfo->contentInfo->id)
            ->addOrderBy('t.day', 'ASC')
            ->addOrderBy('t.startTime', 'ASC')
            ->getQuery();
        $trainings = $query->getResult();
        $params['trainings'] = [];
        $day = null;
        if(count($trainings) > 0 ) {
            foreach($trainings as $training) {
                $params['trainings'][$training->intToDay()][] = ['dayString' => $training->intToDay(),
                    'dayInt' => intval($training->getDay()),
                    'activity' => $training->getActivity(),
                    'color' => $training->getColor(),
                    'startTime' => intval($training->getStartTime()),
                    'endTime' => intval($training->getEndTime()),
                    'id' => $training->getId()
                ];

            }
        }

        return $this->render( '@AppSite/follow/training.html.twig', ['params' => $params, 'form' => $form]);
    }

    /**
     * Remove training
     * @param Request $request
     * @return JsonResponse
     */
    public function removeTrainingAction(Request $request)
    {

        $this->coreHelper = $this->container->get('app.core_helper');
        $result = [
            'error_code' => 0
        ];

        if ($request->getMethod() == 'POST') {
            $userId = intval($request->get('userId'));
            $activityId = intval($request->get('activityId'));
            $em = $this->getDoctrine()->getManager();
            $training = $em->getRepository('AppSiteBundle:Training')->find($activityId);
            if($training == null || $training != null && intval($training->getUserId()) != $userId) {
                $this->get('session')->getFlashBag()->add(
                    'notice',
                    [
                        'alert' => 'danger',
                        'message' => $this->get('translator')->trans('app.training_not_exist')
                    ]
                );

                return new JsonResponse($result);
            }
            $em->remove($training);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'notice',
                [
                    'alert' => 'success',
                    'message' => $this->get('translator')->trans('app.training_remove.success')
                ]
            );
        }

        return new JsonResponse($result);
    }

    /**
     * Add training
     * @param Request $request
     * @return JsonResponse
     */
    public function addTrainingAction(Request $request)
    {
        $this->coreHelper = $this->container->get('app.core_helper');
        $training = new Training();
        $result = [
            'error_code' => 0
        ];
        $form = $this->createForm(TrainingType::class, $training);
        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            $user = null;
            try {
                $this->coreHelper->getContentById($training->getUserId());
            } catch (\Exception $e) {
                $result['error_code'] = 1;
                $result['message'] = $this->get('translator')->trans('app.user_not_exist');
            }
            if($training->getStartTime() >= $training->getEndTime()) {
                $result['error_code'] = 1;
                $result['message'] = $this->get('translator')->trans('app.start_more_than_end');

                return new JsonResponse($result);
            }
            $em = $this->getDoctrine()->getManager();
            $alreadyTraining = $em->getRepository('AppSiteBundle:Training')
                ->getExistingTraining($training->getUserId(), $training->getDay(), $training->getStartTime(), $training->getEndTime());
            if(count($alreadyTraining) > 0) {
                $result['error_code'] = 1;
                $result['message'] = $this->get('translator')->trans('app.already_an_activity');

                return new JsonResponse($result);
            }
            $em->persist($training);
            $em->flush();
            $result['data'] = ['dayString' => $training->intToDay(),
                'dayInt' => $training->getDay(),
                'activity' => $training->getActivity(),
                'color' => $training->getColor(),
                'startTime' => $training->getStartTime(),
                'endTime' => $training->getEndTime(),
                'id' => $training->getId()
            ];

        }

        return new JsonResponse($result);
    }
}

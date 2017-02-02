<?php

namespace App\Bundle\SiteBundle\Controller;

use App\Bundle\SiteBundle\Entity\Training;
use App\Bundle\SiteBundle\Entity\User;
use App\Bundle\SiteBundle\Entity\Visit;
use App\Bundle\SiteBundle\Helper\CoreHelper;
use Doctrine\DBAL\Types\DateType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

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

        return $this->render( '@AppSite/follow/index.html.twig', array('params' => $params));
    }

    public function addPatientAction(Request $request)
    {
        $params = $this->getUserInformation();
        if ($params['error']) {
            return $params['response'];
        } else if (!$params['admin']) {
            return $this->redirect($this->generateUrl('follow-visit-patient'));
        }

        $user = new User();
        $form = $this->createForm($this->get('app.form.type.addpatient'), $user, array());

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $userHelper = $this->get('app.user_helper');
                if ($userHelper->save($user)) {
                    $this->get('session')->getFlashBag()->add(
                        'notice',
                        array(
                            'alert' => 'success',
                            'message' => $this->get('translator')->trans('app.create_user.success')
                        )
                    );
                    unset($user);

                    return $this->redirect($this->generateUrl('follow-index'));
                }
            }
        }

        $params['no_menu'] = true;

        return $this->render( '@AppSite/follow/addpatient.html.twig', array('params' => $params, 'form' => $form->createView()));

    }



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
            $form = $this->createForm($this->get('app.form.type.addvisit'), $visit, array());
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
        $visits = $em->getRepository('AppSiteBundle:Visit')->findBy(array('userId' => $params['user']->versionInfo->contentInfo->id), array('date' => 'ASC'));
        $params['visits'] = array();
        if(count($visits) > 0 ) {
            foreach($visits as $visit) {
                $visitArray = $visit->_toArray();
                $params['visits'][] = array('date' => $visit->getDate()->format('d-m-Y'),
                                            'weight' => floatval($visitArray['weight']),
                                            'fatMass' => floatval($visitArray['fatMass']),
                                            'arm' => floatval($visitArray['arm']),
                                            'thigh' => floatval($visitArray['thigh']),
                                            'chest' => floatval($visitArray['chest']),
                                            'hip' => floatval($visitArray['hip']),
                                            'size' => floatval($visitArray['size']));

            }
            $params['visits_json'] = json_encode($params['visits']);
        }

        return $this->render( '@AppSite/follow/visit.html.twig', array('params' => $params, 'form' => $form));
    }

    public function profilAction($userId = null)
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

        return $this->render( '@AppSite/follow/profil.html.twig', array('params' => $params));
    }

    public function changePasswordAction(Request $request)
    {
        $params = $this->getUserInformation();
        if($params['error']) {
            return $params['response'];
        }


        $this->coreHelper = $this->container->get('app.core_helper');
        $userHelper  = $this->container->get('app.user_helper');
        $user = new User();

        $userHelper->loadUserObjectByEzApiUser($user, $params['user']->versionInfo->contentInfo->id);

        $form = $this->createForm($this->get('app.form.type.changepassword'), $user, array());

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $user->setPassword($user->newPassword);
                $userHelper = $this->get('app.user_helper');
                if ($userHelper->update($user)) {
                    $this->get('session')->getFlashBag()->add(
                        'notice',
                        array(
                            'alert' => 'success',
                            'message' => $this->get('translator')->trans('app.change_password.success')
                        )
                    );
                    unset($user);

                    return $this->redirect($this->generateUrl('follow-index'));
                }
            }
        }

        return $this->render( '@AppSite/follow/changepassword.html.twig', array('params' => $params, 'form' => $form->createView()));
    }

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

        return $this->render( '@AppSite/follow/rations.html.twig', array('params' => $params));
    }

    public function AddVisitAction(Request $request)
    {
        $this->coreHelper = $this->container->get('app.core_helper');
        $visit = new Visit();
        $result = [
            'error_code' => 0
        ];

        $form = $this->createForm($this->get('app.form.type.addvisit'), $visit, array());
        if ($request->getMethod() == 'POST') {
            // Getting data
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

            // tells Doctrine you want to (eventually) save the Product (no queries yet)
            $em->persist($visit);

            // actually executes the queries (i.e. the INSERT query)
            $em->flush();

            $userHelper->loadUserObjectByEzApiUser($userAdd, $visit->getUserId());
            $result['data'] = array('date' => $visit->getDate()->format('d-m-Y'),
                                    'weight' => $visit->getWeight(),
                                    'massG' => $visit->getFatMass(),
                                    'colorFatMass' => $userHelper->getColorFatMass($userAdd),
                                    'arm' => $visit->getArm(),
                                    'chest' => $visit->getChest(),
                                    'hip' => $visit->getHip(),
                                    'thigh' => $visit->getThigh(),
                                    'size' => $visit->getSize()
                                    );

            return new JsonResponse($result);
        }
    }

    public function editProfilAction(Request $request, $userId = null)
    {
        $params = $this->getUserInformation();
        if($params['error']) {
            return $params['response'];
        }

        $this->coreHelper = $this->container->get('app.core_helper');
        if ($userId != null && $params['admin']) {
            $params['user'] = $this->coreHelper->getContentById($userId);
        }


        $this->coreHelper = $this->container->get('app.core_helper');
        $userHelper  = $this->container->get('app.user_helper');
        $user = new User();

        $userHelper->loadUserObjectByEzApiUser($user, $params['user']->versionInfo->contentInfo->id);

        $form = $this->createForm($this->get('app.form.type.editpatient'), $user, array());

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $user->setPassword($user->newPassword);
                $userHelper = $this->get('app.user_helper');
                if ($userHelper->update($user, true)) {
                    $this->get('session')->getFlashBag()->add(
                        'notice',
                        array(
                            'alert' => 'success',
                            'message' => $this->get('translator')->trans('app.edit_patient.success')
                        )
                    );
                    unset($user);

                    return $this->redirect($this->generateUrl('follow-index'));
                }
            }
        }

        return $this->render( '@AppSite/follow/editpatient.html.twig', array('params' => $params, 'form' => $form->createView()));
    }

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
            $form = $this->createForm($this->get('app.form.type.addtraining'), $training, array());
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
        $params['trainings'] = array();
        $day = null;
        if(count($trainings) > 0 ) {
            foreach($trainings as $training) {
                $params['trainings'][$training->intToDay()][] = array('dayString' => $training->intToDay(),
                                                  'dayInt' => intval($training->getDay()),
                                                  'activity' => $training->getActivity(),
                                                  'color' => $training->getColor(),
                                                  'startTime' => intval($training->getStartTime()),
                                                  'endTime' => intval($training->getEndTime()),
                                                  'id' => $training->getId());

            }
        }

        return $this->render( '@AppSite/follow/training.html.twig', array('params' => $params, 'form' => $form));
    }

    public function RemoveTrainingAction(Request $request)
    {
        $this->coreHelper = $this->container->get('app.core_helper');
        $training = new Training();
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
                    array(
                        'alert' => 'danger',
                        'message' => $this->get('translator')->trans('app.training_not_exist')
                    )
                );

                return new JsonResponse($result);
            }
            $em->remove($training);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'notice',
                array(
                    'alert' => 'success',
                    'message' => $this->get('translator')->trans('app.training_remove.success')
                )
            );
        }

        return new JsonResponse($result);
    }

    public function AddTrainingAction(Request $request)
    {
        $this->coreHelper = $this->container->get('app.core_helper');
        $training = new Training();
        $result = [
            'error_code' => 0
        ];

        $form = $this->createForm($this->get('app.form.type.addtraining'), $training, array());
        if ($request->getMethod() == 'POST') {
            // Getting data
            $form->handleRequest($request);
            $user = null;
            try {
                $user = $this->coreHelper->getContentById($training->getUserId());
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

            // tells Doctrine you want to (eventually) save the Product (no queries yet)
            $em->persist($training);

            // actually executes the queries (i.e. the INSERT query)
            $em->flush();

            $result['data'] = array('dayString' => $training->intToDay(),
                                    'dayInt' => $training->getDay(),
                                    'activity' => $training->getActivity(),
                                    'color' => $training->getColor(),
                                    'startTime' => $training->getStartTime(),
                                    'endTime' => $training->getEndTime());

        }

        return new JsonResponse($result);
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
        $params['user'] = $user;
        $params['admin'] = $user->getFieldValue('administrateur')->bool;
        $params['contact'] = $this->container->getParameter('app.email.contact');

        return $params;
    }
}

<?php

namespace App\Bundle\SiteBundle\Controller;

use App\Bundle\SiteBundle\Entity\User;
use App\Bundle\SiteBundle\Entity\Visit;
use App\Bundle\SiteBundle\Form\Type\AddPatientType;
use App\Bundle\SiteBundle\Form\Type\ChangePasswordType;
use App\Bundle\SiteBundle\Form\Type\EditPatientType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;


class PatientController extends ParentController
{

    /**
     * Add patient
     * @param Request $request
     * @return mixed|RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addPatientAction(Request $request)
    {
        $params = $this->getUserInformation();
        if ($params['error']) {
            return $params['response'];
        } else if (!$params['admin']) {
            return $this->redirect($this->generateUrl('follow-visit-patient'));
        }

        $user = new User();
        $form = $this->createForm(AddPatientType::class, $user, [
            'invalid_message' => $this->get('translator')->trans('app.registration.validation.password.no.match')
        ]);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $userHelper = $this->get('app.user_helper');
                if ($userHelper->save($user)) {
                    $this->get('session')->getFlashBag()->add(
                        'notice',
                        [
                            'alert' => 'success',
                            'message' => $this->get('translator')->trans('app.create_user.success')
                        ]
                    );
                    unset($user);

                    return $this->redirect($this->generateUrl('follow-index'));
                }
            }
        }

        $params['no_menu'] = true;

        return $this->render( '@AppSite/follow/addpatient.html.twig', ['params' => $params, 'form' => $form->createView()]);

    }

    public function disabledPatientAction($id)
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
                    'message' => $this->get('translator')->trans('app.patient_disabled.success')
                ]
            );
        }

        return $this->redirectToRoute('follow-visit', ['userId' => $userId]);
    }

    /**
     * Profil patient or admin
     * @param null|string $userId
     * @return mixed|\Symfony\Component\HttpFoundation\Response
     */
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

        return $this->render( '@AppSite/follow/profil.html.twig', ['params' => $params]);
    }

    /**
     * Change password
     * @param Request $request
     * @return mixed|RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
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

        $form = $this->createForm(ChangePasswordType::class, $user, [
            'invalid_message' => $this->get('translator')->trans('app.registration.validation.password.no.match')
        ]);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $user->setPassword($user->newPassword);
                $userHelper = $this->get('app.user_helper');
                if ($userHelper->update($user)) {
                    $this->get('session')->getFlashBag()->add(
                        'notice',
                        [
                            'alert' => 'success',
                            'message' => $this->get('translator')->trans('app.change_password.success')
                        ]
                    );
                    unset($user);

                    return $this->redirect($this->generateUrl('follow-index'));
                }
            }
        }

        return $this->render( '@AppSite/follow/changepassword.html.twig', ['params' => $params, 'form' => $form->createView()]);
    }

    /**
     * Edit profil action
     * @param Request $request
     * @param null $userId
     * @return mixed|RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
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

        $form = $this->createForm(EditPatientType::class, $user);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $user->setPassword($user->newPassword);
                $userHelper = $this->get('app.user_helper');
                if ($userHelper->update($user, true)) {
                    $this->get('session')->getFlashBag()->add(
                        'notice',
                        [
                            'alert' => 'success',
                            'message' => $this->get('translator')->trans('app.edit_patient.success')
                        ]
                    );
                    unset($user);

                    return $this->redirect($this->generateUrl('follow-index'));
                }
            }
        }

        return $this->render( '@AppSite/follow/editpatient.html.twig', ['params' => $params, 'form' => $form->createView()]);
    }

}

<?php
/**
 * This file is part of the MrtEcommerce package.
 *
 * @copyright Copyright (C) Mapi Research Trust
 */

namespace App\Bundle\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\Translator;
use eZ\Publish\API\Repository\Repository;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * AddPatientType Class.
 *
 * @author simoninl
 */
class AddPatientType extends AbstractType
{

    protected $translator;
    protected $repository;

    /**
     * Constructor
     * @param Translator $translator
     * @param Repository $repository
     */
    public function __construct(Translator $translator, Repository $repository)
    {
        $this->translator = $translator;
        $this->repository = $repository;

    }
    /**
     * Build form.
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter) Inheritance of onKernelRequest.
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', 'text')
            ->add('lastName', 'text')
            ->add('email', 'email')
            ->add('birthday', 'date', array(
                'widget' => 'single_text',

                // do not render as type="date", to avoid HTML5 date pickers
                'html5' => false,

                // add a class that can be selected in JavaScript
                'attr' => ['class' => 'js-datepicker inputmov form-control'],
            ))
            ->add('image', 'file', array('required' => false))
            ->add('street', 'text')
            ->add('country', 'text')
            ->add('phone', 'text')
            ->add('postalCode', 'text')
            ->add('city', 'text')
            ->add('height', 'text')
            ->add('weight', 'text')
            ->add('formule', 'choice', array(
                'choices' => array(
                    'Suivi diététique',
                    'Forfaits diététiques',
                    'Pack mov.idiet'
                ),
                'required' => true
            ))
            ->add('password', 'repeated', [ 'type' => 'password', 'invalid_message' => $this->translator->trans('app.registration.validation.password.no.match') ])
            ->add('save', 'submit');
    }

    /**
     * Return registration form name.
     *
     * @return string
     */
    public function getName()
    {
        return 'add_patient';
    }

    /**
     * ConfigureOptions, gets data registration class.
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'App\Bundle\SiteBundle\Entity\User'
            )
        );
    }
}
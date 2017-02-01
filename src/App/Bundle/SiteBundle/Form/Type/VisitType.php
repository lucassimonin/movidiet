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
 * ForgotPasswordNewPasswordType Class.
 *
 * @author simoninl
 */
class VisitType extends AbstractType
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
            ->add('date', 'date', array(
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',

                // do not render as type="date", to avoid HTML5 date pickers
                'html5' => false,

                // add a class that can be selected in JavaScript
                'attr' => [
                    'class' => 'datepicker inputmov form-control',
                    'data-provide' => 'datepicker',
                    'data-date-format' => 'dd-mm-yyyy'
                ]
            ))
            ->add('weight', 'text')
            ->add('fatMass', 'text')
            ->add('arm', 'text')
            ->add('thigh', 'text')
            ->add('chest', 'text')
            ->add('hip', 'text')
            ->add('size', 'text')
            ->add('userId', 'text')
            ->add('save', 'submit');
    }

    /**
     * Return registration form name.
     *
     * @return string
     */
    public function getName()
    {
        return 'add_visit';
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
                'data_class' => 'App\Bundle\SiteBundle\Entity\Visit'
            )
        );
    }
}
<?php
/**
 * This file is part of the MrtEcommerce package.
 *
 * @copyright Copyright (C) Mapi Research Trust
 */

namespace App\Bundle\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * AddPatientType Class.
 *
 * @author simoninl
 */
class AddPatientType extends AbstractType
{
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
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add('account', TextType::class)
            ->add('email', EmailType::class)
            ->add('birthday', DateType::class, array(
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',

                // do not render as type="date", to avoid HTML5 date pickers
                'html5' => false,

                // add a class that can be selected in JavaScript
                'attr' => [
                    'class' => 'datepicker inputmov form-control'
                ]
            ))
            ->add('image', FileType::class, array('required' => false))
            ->add('street', TextType::class)
            ->add('country', TextType::class)
            ->add('phone', TextType::class)
            ->add('postalCode', TextType::class)
            ->add('city', TextType::class)
            ->add('height', TextType::class)
            ->add('formule', ChoiceType::class, array(
                'choices' => array(
                    'Suivi diététique',
                    'Forfaits diététiques',
                    'Pack mov.idiet'
                ),
                'required' => true
            ))
            ->add('sex', ChoiceType::class, array(
                'choices' => array(
                    'Homme',
                    'Femme',
                ),
                'required' => true
            ))
            ->add('password', RepeatedType::class, [ 'type' => 'password', 'invalid_message' => $options['invalid_message'] ])
            ->add('save', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'invalid_message' => null
        ]);
    }
}

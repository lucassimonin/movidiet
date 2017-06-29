<?php
/**
 * This file is part of the MrtEcommerce package.
 *
 * @copyright Copyright (C) Mapi Research Trust
 */

namespace App\Bundle\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\Translator;
use eZ\Publish\API\Repository\Repository;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * EditPatientType Class.
 *
 * @author simoninl
 */
class EditPatientType extends AbstractType
{
    /**
     * Build form.
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     *
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add('email', EmailType::class)
            ->add('image', FileType::class, ['required' => false])
            ->add('street', TextType::class)
            ->add('country', TextType::class)
            ->add('phone', TextType::class)
            ->add('postalCode', TextType::class)
            ->add('city', TextType::class)
            ->add('sex', ChoiceType::class, [
                'choices' => [
                    'app.patient.man',
                    'app.patient.woman',
                ],
                'required' => true
            ])
            ->add('save', SubmitType::class);
    }
}

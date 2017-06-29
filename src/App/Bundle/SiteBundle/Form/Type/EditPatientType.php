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
            ->add('firstName', 'text')
            ->add('lastName', 'text')
            ->add('email', 'email')
            ->add('image', 'file', array('required' => false))
            ->add('street', 'text')
            ->add('country', 'text')
            ->add('phone', 'text')
            ->add('postalCode', 'text')
            ->add('city', 'text')
            ->add('sex', 'choice', array(
                'choices' => array(
                    'Homme',
                    'Femme',
                ),
                'required' => true
            ))
            ->add('save', 'submit');
    }
}

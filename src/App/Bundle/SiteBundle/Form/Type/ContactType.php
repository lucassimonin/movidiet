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
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * ContactType Class.
 *
 * @author simoninl
 */
class ContactType extends AbstractType
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
            ->add('name', TextType::class, ['required' => true])
            ->add('email', EmailType::class, ['required' => true])
            ->add('subject', ChoiceType::class, [
                'choices' => [
                    'app.form.first_check',
                    'app.form.follow',
                    'app.form.forfait',
                    'app.form.pack',
                    'app.form.other'
                ],
                'required' => true
            ])
            ->add('message', TextareaType::class, ['required' => true]);
    }
}

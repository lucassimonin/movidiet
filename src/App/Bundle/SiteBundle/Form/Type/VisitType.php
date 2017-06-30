<?php
/**
 *
 * @copyright Copyright (C) Mapi Research Trust
 */

namespace App\Bundle\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\Translator;
use eZ\Publish\API\Repository\Repository;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * VisitType Class.
 *
 * @author simoninl
 */
class VisitType extends AbstractType
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
            ->add('date', DateType::class, array(
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'html5' => false,
                'attr' => [
                    'class' => 'datepicker inputmov form-control'
                ]
            ))
            ->add('weight', TextType::class)
            ->add('fatMass', TextType::class)
            ->add('arm', TextType::class)
            ->add('thigh', TextType::class)
            ->add('chest', TextType::class)
            ->add('hip', TextType::class)
            ->add('size', TextType::class)
            ->add('userId', TextType::class)
            ->add('save', SubmitType::class);
    }
}

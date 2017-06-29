<?php

namespace App\Bundle\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\Translator;
use eZ\Publish\API\Repository\Repository;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * TrainingType Class.
 *
 * @author simoninl
 */
class TrainingType extends AbstractType
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
            ->add('day', ChoiceType::class, [
                'choices' => [
                    'app.monday',
                    'app.tuesday',
                    'app.wednesday',
                    'app.thursday',
                    'app.friday',
                    'app.saturday',
                    'app.sunday'
                ],
                'required' => true
            ])
            ->add('startTime', TimeType::class, [
                'input'  => 'timestamp',
                'widget' => 'choice',
            ])
            ->add('endTime', TimeType::class, [
                'input'  => 'timestamp',
                'widget' => 'choice',
            ])
            ->add('userId', TextType::class)
            ->add('activity', TextType::class)
            ->add('color', TextType::class)
            ->add('save', SubmitType::class);
    }
}

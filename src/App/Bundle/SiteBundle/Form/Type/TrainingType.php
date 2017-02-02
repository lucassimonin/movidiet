<?php

namespace App\Bundle\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\Translator;
use eZ\Publish\API\Repository\Repository;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * ChangePasswordType Class.
 *
 * @author simoninl
 */
class TrainingType extends AbstractType
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
            ->add('day', 'choice', array(
                'choices' => array(
                    'Lundi',
                    'Mardi',
                    'Mercredi',
                    'Jeudi',
                    'Vendredi',
                    'Samedi',
                    'Dimanche'
                ),
                'required' => true
            ))
            ->add('startTime', TimeType::class, array(
                'input'  => 'timestamp',
                'widget' => 'choice',
            ))
            ->add('endTime', TimeType::class, array(
                'input'  => 'timestamp',
                'widget' => 'choice',
            ))
            ->add('userId', 'text')
            ->add('activity', 'text')
            ->add('color', 'text')
            ->add('save', 'submit');
    }

    /**
     * Return registration form name.
     *
     * @return string
     */
    public function getName()
    {
        return 'add_training';
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
                'data_class' => 'App\Bundle\SiteBundle\Entity\Training'
            )
        );
    }
}
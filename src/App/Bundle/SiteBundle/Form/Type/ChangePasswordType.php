<?php

namespace App\Bundle\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\Translator;
use eZ\Publish\API\Repository\Repository;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * ChangePasswordType Class.
 *
 * @author simoninl
 */
class ChangePasswordType extends AbstractType
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
            ->add('newPassword', 'repeated', [ 'type' => 'password', 'invalid_message' => $this->translator->trans('app.registration.validation.password.no.match') ])
            ->add('save', 'submit');
    }

    /**
     * Return registration form name.
     *
     * @return string
     */
    public function getName()
    {
        return 'change_password';
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
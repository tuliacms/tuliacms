<?php

declare(strict_types=1);

namespace Tulia\Cms\Installator\UI\Web\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Adam Banaszkiewicz
 */
class DatabaseForm extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('host', Type\TextType::class, [
                'label' => 'host',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('name', Type\TextType::class, [
                'label' => 'databaseName',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('port', Type\TextType::class, [
                'label' => 'port',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('username', Type\TextType::class, [
                'label' => 'username',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('prefix', Type\TextType::class, [
                'label' => 'tablePrefix',
                'help' => 'tablePrefixInfo',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('password', Type\PasswordType::class, [
                'label' => 'password',
                'translation_domain' => 'messages',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('translation_domain', 'installator');
    }
}

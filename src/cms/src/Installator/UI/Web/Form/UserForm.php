<?php

declare(strict_types=1);

namespace Tulia\Cms\Installator\UI\Web\Form\Installator;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Validator\Constraints as Assert;
use Tulia\Cms\User\Validator\Constraints\EmailUnique;
use Tulia\Cms\User\Validator\Constraints\Password;
use Tulia\Cms\User\Validator\Constraints\Username;
use Tulia\Cms\User\Validator\Constraints\UsernameUnique;

/**
 * @author Adam Banaszkiewicz
 */
class UserForm extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', Type\TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Username(),
                ],
            ])
            ->add('password', Type\RepeatedType::class, [
                'type' => Type\PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'required' => true,
                'constraints' => [
                    new Password(),
                    new Assert\NotBlank(),
                ],
                'first_options'  => [
                    'label' => 'password',
                    'attr' => ['autocomplete' => 'off'],
                ],
                'second_options' => [
                    'label' => 'passwordRepeat',
                    'attr' => ['autocomplete' => 'off'],
                ],
            ])
            ->add('email', Type\EmailType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Email(),
                ],
            ])
        ;
    }
}

<?php

declare(strict_types=1);

namespace Tulia\Cms\User\UserInterface\Web\Form\Extension;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Tulia\Cms\User\Infrastructure\Framework\Validator\Constraints\Password;
use Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType;
use Tulia\Cms\User\UserInterface\Web\Form\UserForm\UserForm;
use Tulia\Component\FormSkeleton\Extension\AbstractExtension;
use Tulia\Component\FormSkeleton\Section\SectionsBuilderInterface;

/**
 * @author Adam Banaszkiewicz
 */
class SecurityExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $roles = [
            'Administrator' => 'ROLE_ADMIN',
            'User' => 'ROLE_USER',
        ];

        $builder
            ->add('roles', Type\ChoiceType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Choice(['choices' => $roles, 'multiple' => true]),
                ],
                'multiple' => true,
                'choices' => $roles,
            ])
            ->add('enabled', FormType\YesNoType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
        ;

        $passwordConstraints = [ new Password() ];

        if ($options['password_required']) {
            $passwordConstraints[] = new Assert\NotBlank();
        }

        $builder
            ->add('password', Type\RepeatedType::class, [
                'type' => Type\PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'required' => $options['password_required'],
                'constraints' => $passwordConstraints,
                'first_options'  => [
                    'label' => 'password',
                    'attr' => ['autocomplete' => 'off'],
                ],
                'second_options' => [
                    'label' => 'passwordRepeat',
                    'attr' => ['autocomplete' => 'off'],
                ],
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getSections(SectionsBuilderInterface $builder): void
    {
        $builder
            ->add('security', [
                'view' => '@backend/user/user/parts/security.tpl',
                'priority' => 1000,
                'fields' => ['password', 'enabled', 'roles'],
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function supports(FormTypeInterface $formType, array $options, $data = null): bool
    {
        return $formType instanceof UserForm;
    }
}

<?php

declare(strict_types=1);

namespace Tulia\Cms\User\UserInterface\Web\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Validator\Constraints as Assert;
use Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType;
use Tulia\Cms\User\Infrastructure\Framework\Validator\Constraints\Password;

/**
 * @author Adam Banaszkiewicz
 */
class PasswordForm extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password', Type\RepeatedType::class, [
                'type' => Type\PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Password(),
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
            ->add('save', FormType\SubmitType::class)
        ;
    }
}

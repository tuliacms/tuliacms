<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Infrastructure\Cms\Settings;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\AbstractType;

/**
 * @author Adam Banaszkiewicz
 */
class SettingsForm extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password_min_length', Type\TextType::class, [
                'label' => 'passwordMinLength',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Range([ 'min' => 4 ]),
                ],
                'translation_domain' => 'users',
            ])
            ->add('password_min_digits', Type\TextType::class, [
                'label' => 'passwordMinDigits',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Range([ 'min' => 0 ]),
                ],
                'translation_domain' => 'users',
            ])
            ->add('password_min_special_chars', Type\TextType::class, [
                'label' => 'passwordMinSpecialChars',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Range([ 'min' => 0 ]),
                ],
                'translation_domain' => 'users',
            ])
            ->add('password_min_big_letters', Type\TextType::class, [
                'label' => 'passwordMinBigLetters',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Range([ 'min' => 0 ]),
                ],
                'translation_domain' => 'users',
            ])
            ->add('password_min_small_letters', Type\TextType::class, [
                'label' => 'passwordMinSmallLetters',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Range([ 'min' => 0 ]),
                ],
                'translation_domain' => 'users',
            ])
            ->add('username_min_length', Type\TextType::class, [
                'label' => 'usernameMinLength',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Range([ 'min' => 4 ]),
                ],
                'translation_domain' => 'users',
            ]);
    }
}

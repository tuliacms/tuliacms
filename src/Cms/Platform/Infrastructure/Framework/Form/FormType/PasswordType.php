<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType as SymfonyPasswordType;
use Tulia\Cms\User\Infrastructure\Framework\Validator\Constraints\Password;

/**
 * @author Adam Banaszkiewicz
 */
class PasswordType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('constraints', [
            new Password()
        ]);
    }

    public function getParent(): string
    {
        return SymfonyPasswordType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'input';
    }
}

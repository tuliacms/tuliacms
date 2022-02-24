<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Infrastructure\Framework\Form\FormType\UserRoles;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Adam Banaszkiewicz
 */
class UserRolesType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        // @todo Fetch available roles from centralized place.
        $roles = [
            'Administrator' => 'ROLE_ADMIN',
            'User' => 'ROLE_USER',
        ];

        $resolver->setDefault('choices', $roles);
        $resolver->setDefault('multiple', true);
        $resolver->setDefault('constraints', [
            new Assert\Choice(['choices' => $roles, 'multiple' => true]),
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}

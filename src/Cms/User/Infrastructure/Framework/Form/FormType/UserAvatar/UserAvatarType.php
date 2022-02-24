<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Infrastructure\Framework\Form\FormType\UserAvatar;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

/**
 * @author Adam Banaszkiewicz
 */
class UserAvatarType extends AbstractType
{
    public function getParent(): string
    {
        return FileType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'user_avatar';
    }
}

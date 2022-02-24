<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Infrastructure\Framework\Form\FormType\UserAvatar;

use Symfony\Component\HttpFoundation\File\File;

/**
 * @author Adam Banaszkiewicz
 */
class UserAvatarFile extends File
{
    private ?string $previewPath;

    public function __construct(string $path, bool $checkPath = true, string $previewPath = null)
    {
        parent::__construct($path, $checkPath);

        $this->previewPath = $previewPath;
    }

    public function getPreviewPath(): ?string
    {
        return $this->previewPath;
    }
}

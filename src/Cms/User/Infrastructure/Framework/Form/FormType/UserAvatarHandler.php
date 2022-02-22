<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Infrastructure\Framework\Form\FormType;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\FieldTypeHandler\FieldTypeHandlerInterface;
use Tulia\Cms\User\Application\Service\Avatar\UploaderInterface;

/**
 * @author Adam Banaszkiewicz
 */
class UserAvatarHandler implements FieldTypeHandlerInterface
{
    private UploaderInterface $uploader;
    private ?string $oldAvatar = null;

    public function __construct(UploaderInterface $uploader)
    {
        $this->uploader = $uploader;
    }

    public function prepareValueToForm($value)
    {
        if ($value) {
            $this->oldAvatar = $value;

            if ($this->uploader->avatarExists($value)) {
                return new File($this->uploader->getFilepath($value));
            } else {
                $this->oldAvatar = null;
                return null;
            }
        }

        return null;
    }

    public function handle($value)
    {
        if ($value instanceof UploadedFile) {
            try {
                $newAvatar = $this->uploader->upload($value);
            } catch (\Throwable $exception) {
                throw $exception;
            }

            if ($this->oldAvatar) {
                $this->uploader->removeUploaded($this->oldAvatar);
            }

            return $newAvatar;
        }

        return null;
    }
}

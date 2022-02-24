<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Infrastructure\Framework\Form\FormType\UserAvatar;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\FieldTypeHandler\FieldTypeHandlerInterface;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\Field;
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

    public function prepareValueToForm(Field $field, $value)
    {
        if ($value && $this->uploader->avatarExists($value)) {
            $this->oldAvatar = $value;
            return new UserAvatarFile($this->uploader->getFilepath($value), true, $value);
        }

        return null;
    }

    public function handle(Field $field, $value)
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

        return $this->oldAvatar;
    }
}

<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Application\Service\Avatar;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Tulia\Cms\User\Infrastructure\Cms\Metadata\UserMetadataEnum;
use Tulia\Cms\User\Query\Model\User;

/**
 * @author Adam Banaszkiewicz
 */
class Uploader implements UploaderInterface
{
    protected string $publicDir;

    public function __construct(string $publicDir)
    {
        $this->publicDir = $publicDir;
    }

    /**
     * {@inheritdoc}
     */
    public function uploadForUser(User $user, FormInterface $form, string $field = UserMetadataEnum::AVATAR): string
    {
        /** @var UploadedFile $avatarFile */
        $avatarFile = $form[$field]->getData();

        if (!$avatarFile) {
            return '';
        }

        $newAvatar = $this->upload($avatarFile);
        $oldAvatar = $user->getMeta(UserMetadataEnum::AVATAR);

        $user->setMeta(UserMetadataEnum::AVATAR, $newAvatar);

        if ($oldAvatar) {
            $this->removeUploaded($oldAvatar);
        }

        return $newAvatar;
    }

    /**
     * {@inheritdoc}
     */
    public function upload(UploadedFile $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        // This is needed to safely include the file name as part of the URL
        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
        $newFilename  = $safeFilename . '-' . uniqid('', true) . '.' . $file->guessExtension();

        $destination = '/uploads/user/avatars/' . date('Y/m');

        $file->move(
            $this->publicDir . $destination,
            $newFilename
        );

        return $destination . '/' . $newFilename;
    }

    /**
     * {@inheritdoc}
     */
    public function removeUploadedForUser(User $user): void
    {
        if (! $user->getMeta(UserMetadataEnum::AVATAR)) {
            return;
        }

        $this->removeUploaded($user->getMeta(UserMetadataEnum::AVATAR));

        $user->setMeta(UserMetadataEnum::AVATAR, null);
    }

    /**
     * {@inheritdoc}
     */
    public function removeUploaded(string $filepath): void
    {
        if (is_file($this->publicDir . $filepath)) {
            @ unlink($this->publicDir . $filepath);
        }
    }
}

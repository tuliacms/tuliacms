<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Application\Service\Avatar;

use Symfony\Component\HttpFoundation\File\UploadedFile;

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

    public function removeUploaded(string $filepath): void
    {
        if (is_file($this->publicDir . $filepath)) {
            @ unlink($this->publicDir . $filepath);
        }
    }

    public function getFilepath(string $filepath): string
    {
        return $this->publicDir . $filepath;
    }

    public function avatarExists(string $filepath): bool
    {
        return is_file($this->publicDir . $filepath);
    }
}

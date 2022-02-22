<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Application\Service\Avatar;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @author Adam Banaszkiewicz
 */
interface UploaderInterface
{
    /**
     * Uploads file in `/public/uploads` directory, and returns path to this file.
     * @throws FileException
     */
    public function upload(UploadedFile $file): string;

    public function removeUploaded(string $filepath): void;

    public function getFilepath(string $filepath): string;

    public function avatarExists(string $filepath): bool;
}

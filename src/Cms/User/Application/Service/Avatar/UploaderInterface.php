<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Application\Service\Avatar;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Tulia\Cms\User\Query\Model\User;

/**
 * @author Adam Banaszkiewicz
 */
interface UploaderInterface
{
    /**
     * @param User $user
     * @param FormInterface $form
     * @param string $field
     *
     * @return string
     *
     * @throws FileException
     */
    public function uploadForUser(User $user, FormInterface $form, string $field = 'avatar'): string;

    /**
     * Uploads file in `/public/uploads` directory, and returns path to this file.
     *
     * @param UploadedFile $file
     *
     * @return string
     *
     * @throws FileException
     */
    public function upload(UploadedFile $file): string;

    /**
     * @param User $user
     */
    public function removeUploadedForUser(User $user): void;

    /**
     * @param string $filepath
     */
    public function removeUploaded(string $filepath): void;
}

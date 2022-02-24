<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Application\UseCase;

use Tulia\Cms\User\Application\Service\Avatar\UploaderInterface;
use Tulia\Cms\User\Domain\WriteModel\Model\User;
use Tulia\Cms\User\Domain\WriteModel\UserRepositoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class RemoveUser
{
    private UploaderInterface $uploader;
    private UserRepositoryInterface $repository;

    public function __construct(
        UploaderInterface $uploader,
        UserRepositoryInterface $repository
    ) {
        $this->uploader = $uploader;
        $this->repository = $repository;
    }

    public function __invoke(User $user): void
    {
        $this->repository->delete($user);

        if ($user->attribute('avatar')) {
            $this->uploader->removeUploaded($user->attribute('avatar')->getValue());
        }
    }
}

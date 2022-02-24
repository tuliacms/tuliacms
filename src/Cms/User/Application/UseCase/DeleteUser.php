<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Application\UseCase;

use Tulia\Cms\Shared\Domain\WriteModel\ActionsChain\AggregateActionsChainInterface;
use Tulia\Cms\Shared\Infrastructure\Bus\Event\EventBusInterface;
use Tulia\Cms\User\Application\Service\Avatar\UploaderInterface;
use Tulia\Cms\User\Domain\WriteModel\Event\UserDeleted;
use Tulia\Cms\User\Domain\WriteModel\Exception\CannotDeleteYourselfException;
use Tulia\Cms\User\Domain\WriteModel\Model\User;
use Tulia\Cms\User\Domain\WriteModel\UserRepositoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class DeleteUser
{
    private UploaderInterface $uploader;
    private UserRepositoryInterface $repository;
    private EventBusInterface $eventDispatcher;
    private AggregateActionsChainInterface $actionsChain;

    public function __construct(
        UploaderInterface $uploader,
        UserRepositoryInterface $repository,
        EventBusInterface $eventDispatcher,
        AggregateActionsChainInterface $actionsChain
    ) {
        $this->uploader = $uploader;
        $this->repository = $repository;
        $this->eventDispatcher = $eventDispatcher;
        $this->actionsChain = $actionsChain;
    }

    /**
     * @throws CannotDeleteYourselfException
     */
    public function __invoke(User $user): void
    {
        $this->actionsChain->execute('delete', $user);

        $this->repository->delete($user);

        if ($user->attribute('avatar')) {
            $this->uploader->removeUploaded($user->attribute('avatar')->getValue());
        }

        $this->eventDispatcher->dispatch(new UserDeleted($user->getId()->getValue()));
    }
}

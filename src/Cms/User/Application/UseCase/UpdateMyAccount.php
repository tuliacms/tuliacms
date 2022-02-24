<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Application\UseCase;

use Tulia\Cms\Attributes\Domain\WriteModel\Model\Attribute;
use Tulia\Cms\Shared\Domain\WriteModel\ActionsChain\AggregateActionsChainInterface;
use Tulia\Cms\Shared\Infrastructure\Bus\Event\EventBusInterface;
use Tulia\Cms\User\Application\Service\Avatar\UploaderInterface;
use Tulia\Cms\User\Domain\WriteModel\Model\User;
use Tulia\Cms\User\Domain\WriteModel\UserRepositoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class UpdateMyAccount extends AbstractUserUserCase
{
    private UploaderInterface $uploader;

    public function __construct(
        UserRepositoryInterface $repository,
        EventBusInterface $eventDispatcher,
        AggregateActionsChainInterface $actionsChain,
        UploaderInterface $uploader
    ) {
        parent::__construct($repository, $eventDispatcher, $actionsChain);

        $this->uploader = $uploader;
    }

    /**
     * @param Attribute[] $attributes
     */
    public function __invoke(User $user, array $attributes): void
    {
        $data = $this->flattenAttributes($attributes);
        $attributes = $this->removeModelsAttributes($attributes);

        if ($data['remove_avatar'] && $data['avatar']) {
            $this->uploader->removeUploaded($data['avatar']);
        }

        $user->updateAttributes($attributes);
        $user->changeLocale($data['locale']);

        $this->update($user);
    }
}

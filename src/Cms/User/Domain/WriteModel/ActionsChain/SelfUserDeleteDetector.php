<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Domain\WriteModel\ActionsChain;

use Tulia\Cms\Shared\Domain\WriteModel\ActionsChain\AggregateActionInterface;
use Tulia\Cms\Shared\Domain\WriteModel\Model\AggregateRoot;
use Tulia\Cms\User\Application\Service\AuthenticatedUserProviderInterface;
use Tulia\Cms\User\Domain\WriteModel\Exception\CannotDeleteYourselfException;
use Tulia\Cms\User\Domain\WriteModel\Model\User;

/**
 * @author Adam Banaszkiewicz
 */
class SelfUserDeleteDetector implements AggregateActionInterface
{
    protected AuthenticatedUserProviderInterface $authenticatedUserProvider;

    public function __construct(AuthenticatedUserProviderInterface $authenticatedUserProvider)
    {
        $this->authenticatedUserProvider = $authenticatedUserProvider;
    }

    public static function listen(): array
    {
        return [
            'delete' => 100,
        ];
    }

    public static function supports(): string
    {
        return User::class;
    }

    /**
     * @throws CannotDeleteYourselfException
     */
    public function execute(AggregateRoot $user): void
    {
        if ($user->getId()->getValue() === $this->authenticatedUserProvider->getUser()->getId()) {
            throw new CannotDeleteYourselfException('Cannot delete Yourself.');
        }
    }
}

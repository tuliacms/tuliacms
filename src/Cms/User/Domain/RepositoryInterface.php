<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Domain;

use Tulia\Cms\User\Domain\ValueObject\AggregateId;
use Tulia\Cms\User\Domain\Aggregate\User;
use Tulia\Cms\User\Domain\Exception\UserNotFoundException;

/**
 * @author Adam Banaszkiewicz
 */
interface RepositoryInterface
{
    /**
     * @param AggregateId $id
     * @return User
     * @throws UserNotFoundException
     */
    public function find(AggregateId $id): User;

    public function save(User $user): void;

    public function delete(User $user): void;
}

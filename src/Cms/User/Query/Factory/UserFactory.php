<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Query\Factory;

use Tulia\Cms\User\Query\Model\User;
use Tulia\Cms\Shared\Ports\Infrastructure\Utils\Uuid\UuidGeneratorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class UserFactory implements UserFactoryInterface
{
    protected UuidGeneratorInterface $uuidGenerator;

    public function __construct(UuidGeneratorInterface $uuidGenerator)
    {
        $this->uuidGenerator = $uuidGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function createNew(array $data = []): User
    {
        return User::buildFromArray(array_merge($data, [
            'id' => $this->uuidGenerator->generate(),
            'enabled' => true,
        ]));
    }
}

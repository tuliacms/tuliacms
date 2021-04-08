<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Query\Factory;

use Tulia\Cms\User\Infrastructure\Cms\Metadata\Loader;
use Tulia\Cms\User\Query\Model\User;
use Tulia\Cms\Platform\Shared\Uuid\UuidGeneratorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class UserFactory implements UserFactoryInterface
{
    /**
     * @var UuidGeneratorInterface
     */
    protected $uuidGenerator;

    /**
     * @var Loader
     */
    protected $loader;

    /**
     * @param UuidGeneratorInterface $uuidGenerator
     * @param Loader $loader
     */
    public function __construct(UuidGeneratorInterface $uuidGenerator, Loader $loader)
    {
        $this->uuidGenerator = $uuidGenerator;
        $this->loader        = $loader;
    }

    /**
     * {@inheritdoc}
     */
    public function createNew(array $data = []): User
    {
        $user = User::buildFromArray(array_merge($data, [
            'id' => $this->uuidGenerator->generate(),
            'enabled' => true,
        ]));

        $this->loader->load($user);

        return $user;
    }
}

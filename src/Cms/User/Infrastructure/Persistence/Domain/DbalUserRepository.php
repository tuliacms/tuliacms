<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Infrastructure\Persistence\Domain;

use Tulia\Cms\Attributes\Domain\WriteModel\AttributesRepository;
use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\ConnectionInterface;
use Tulia\Cms\Shared\Infrastructure\Utils\Hydrator\HydratorInterface;
use Tulia\Cms\Shared\Infrastructure\Utils\Uuid\UuidGeneratorInterface;
use Tulia\Cms\User\Domain\WriteModel\Exception\UserNotFoundException;
use Tulia\Cms\User\Domain\WriteModel\Model\AggregateId;
use Tulia\Cms\User\Domain\WriteModel\Model\User;
use Tulia\Cms\User\Domain\WriteModel\UserRepositoryInterface;
use Tulia\Cms\User\Infrastructure\Cms\Metadata\UserMetadataEnum;

/**
 * @author Adam Banaszkiewicz
 */
class DbalUserRepository implements UserRepositoryInterface
{
    private ConnectionInterface $connection;
    private DbalPersister $persister;
    private HydratorInterface $hydrator;
    private AttributesRepository $attributeRepository;
    private UuidGeneratorInterface $uuidGenerator;

    public function __construct(
        ConnectionInterface $connection,
        DbalPersister $persister,
        HydratorInterface $hydrator,
        AttributesRepository $attributeRepository,
        UuidGeneratorInterface $uuidGenerator
    ) {
        $this->connection = $connection;
        $this->persister = $persister;
        $this->hydrator = $hydrator;
        $this->attributeRepository = $attributeRepository;
        $this->uuidGenerator = $uuidGenerator;
    }

    public function generateNextId(): AggregateId
    {
        return new AggregateId($this->uuidGenerator->generate());
    }

    /**
     * {@inheritdoc}
     */
    public function find(AggregateId $id): User
    {
        $user = $this->connection->fetchAllAssociative('
            SELECT *
            FROM #__user AS tm
            WHERE tm.id = :id
            LIMIT 1', [
            'id' => $id->getId(),
        ]);

        if (empty($user)) {
            throw new UserNotFoundException();
        }

        $user = reset($user);

        /** @var User $aggregate */
        $aggregate = $this->hydrator->hydrate([
            'id'       => new AggregateId($user['id']),
            'username' => $user['username'],
            'password' => $user['password'],
            'email'    => $user['email'],
            'locale'   => $user['locale'],
            'enabled'  => $user['enabled'] === '1',
            'accountExpired' => $user['account_expired'] === '1',
            'credentialsExpired' => $user['credentials_expired'] === '1',
            'accountLocked' => $user['account_locked'] === '1',
            'roles'    => json_decode($user['roles'], true),
            'attributes' => $this->attributeRepository->findAll(UserMetadataEnum::TYPE, $id->getId(), []),
        ], User::class);

        return $aggregate;
    }

    public function save(User $user): void
    {
        $data = $this->extract($user);

        $this->connection->transactional(function () use ($data) {
            if ($this->recordExists($data['id'])) {
                $this->persister->update($data);
            } else {
                $this->persister->insert($data);
            }

            $this->attributeRepository->persist(UserMetadataEnum::TYPE, $data['id'], $data['attributes']);
        });
    }

    public function delete(User $user): void
    {
        $data = $this->extract($user);

        $this->connection->transactional(function () use ($data) {
            $this->persister->delete($data);
            $this->attributeRepository->delete(UserMetadataEnum::TYPE, $data['id']);
        });
    }

    /**
     * @param string $id
     *
     * @return bool
     */
    private function recordExists(string $id): bool
    {
        $result = $this->connection->fetchAllAssociative('SELECT id FROM #__user WHERE id = :id LIMIT 1', ['id' => $id]);

        return isset($result[0]['id']) && $result[0]['id'] === $id;
    }

    private function extract(User $node): array
    {
        $data = $this->hydrator->extract($node);
        $data['id'] = $node->getId()->getId();

        return $data;
    }
}

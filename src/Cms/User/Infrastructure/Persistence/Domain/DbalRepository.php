<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Infrastructure\Persistence\Domain;

use Tulia\Cms\Metadata\Domain\WriteModel\MetadataRepository;
use Tulia\Cms\Platform\Infrastructure\DataManipulation\Hydrator\HydratorInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;
use Tulia\Cms\User\Domain\Aggregate\User;
use Tulia\Cms\User\Domain\Exception\UserNotFoundException;
use Tulia\Cms\User\Domain\RepositoryInterface;
use Tulia\Cms\User\Domain\ValueObject\AggregateId;
use Tulia\Cms\User\Infrastructure\Cms\Metadata\UserMetadataEnum;

/**
 * @author Adam Banaszkiewicz
 */
class DbalRepository implements RepositoryInterface
{
    protected ConnectionInterface $connection;
    protected DbalPersister $persister;
    protected HydratorInterface $hydrator;
    protected MetadataRepository $metadataRepository;

    public function __construct(
        ConnectionInterface $connection,
        DbalPersister $persister,
        HydratorInterface $hydrator,
        MetadataRepository $metadataRepository
    ) {
        $this->connection = $connection;
        $this->persister = $persister;
        $this->hydrator = $hydrator;
        $this->metadataRepository = $metadataRepository;
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
            'metadata' => $this->metadataRepository->findAll(UserMetadataEnum::TYPE, $id->getId(), []),
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

            $this->metadataRepository->persist(UserMetadataEnum::TYPE, $data['id'], $data['metadata']);
        });
    }

    public function delete(User $user): void
    {
        $data = $this->extract($user);

        $this->connection->transactional(function () use ($data) {
            $this->persister->delete($data);
            $this->metadataRepository->delete(UserMetadataEnum::TYPE, $data['id']);
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

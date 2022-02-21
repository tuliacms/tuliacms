<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Infrastructure\Persistence\Domain;

use Tulia\Cms\Attributes\Domain\WriteModel\AttributesRepository;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\Field;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Service\ContentTypeRegistryInterface;
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
    private ContentTypeRegistryInterface $contentTypeRegistry;

    public function __construct(
        ConnectionInterface $connection,
        DbalPersister $persister,
        HydratorInterface $hydrator,
        AttributesRepository $attributeRepository,
        UuidGeneratorInterface $uuidGenerator,
        ContentTypeRegistryInterface $contentTypeRegistry
    ) {
        $this->connection = $connection;
        $this->persister = $persister;
        $this->hydrator = $hydrator;
        $this->attributeRepository = $attributeRepository;
        $this->uuidGenerator = $uuidGenerator;
        $this->contentTypeRegistry = $contentTypeRegistry;
    }

    public function generateNextId(): AggregateId
    {
        return new AggregateId($this->uuidGenerator->generate());
    }

    public function find(string $id): ?User
    {
        $user = $this->connection->fetchAllAssociative('
            SELECT *
            FROM #__user AS tm
            WHERE tm.id = :id
            LIMIT 1', [
            'id' => $id,
        ]);

        if (empty($user)) {
            return null;
        }

        $nodeType = $this->contentTypeRegistry->get('user');
        $attributesInfo = $this->buildAttributesMapping($nodeType->getFields());
        $attributes = $this->attributeRepository->findAll('user', $id, $attributesInfo);

        $user = reset($user);
        $user['attributes'] =  $attributes;
        $user['roles'] = json_decode($user['roles'], true);

        return User::fromArray($user);
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

    private function recordExists(string $id): bool
    {
        $result = $this->connection->fetchAllAssociative('SELECT id FROM #__user WHERE id = :id LIMIT 1', ['id' => $id]);

        return isset($result[0]['id']) && $result[0]['id'] === $id;
    }

    private function extract(User $user): array
    {
        $data = $this->hydrator->extract($user);
        $data['id'] = $user->getId()->getValue();

        return $data;
    }

    /**
     * @param Field[] $fields
     */
    private function buildAttributesMapping(array $fields, string $prefix = ''): array
    {
        $result = [];

        foreach ($fields as $field) {
            if ($field->isType('repeatable')) {
                foreach ($this->buildAttributesMapping($field->getChildren(), $prefix.$field->getCode().'.') as $code => $subfield) {
                    $result[$code] = $subfield;
                }
            } else {
                $result[$prefix.$field->getCode()] = [
                    'is_multilingual' => $field->isMultilingual(),
                    'is_compilable' => $field->hasFlag('compilable'),
                    'has_nonscalar_value' => $field->hasNonscalarValue(),
                ];

                if ($field->hasFlag('compilable')) {
                    $result[$prefix.$field->getCode().':compiled'] = [
                        'is_multilingual' => $field->isMultilingual(),
                        'is_compilable' => false,
                        'has_nonscalar_value' => $field->hasNonscalarValue(),
                    ];
                }
            }
        }

        return $result;
    }
}

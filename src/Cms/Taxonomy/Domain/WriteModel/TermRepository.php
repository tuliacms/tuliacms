<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\WriteModel;

use Tulia\Cms\Metadata\Domain\WriteModel\MetadataRepository;
use Tulia\Cms\Platform\Infrastructure\Bus\Event\EventBusInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Utils\Uuid\UuidGeneratorInterface;
use Tulia\Cms\Taxonomy\Domain\Metadata\TermMetadataEnum;
use Tulia\Cms\Taxonomy\Domain\WriteModel\ActionsChain\TermActionsChainInterface;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Event\TermDeleted;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Event\TermUpdated;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Exception\TermNotFoundException;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\Term;
use Tulia\Cms\Taxonomy\Ports\Infrastructure\Persistence\Domain\WriteModel\TermWriteStorageInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class TermRepository
{
    private TermWriteStorageInterface $storage;

    private CurrentWebsiteInterface $currentWebsite;

    private MetadataRepository $metadataRepository;

    private UuidGeneratorInterface $uuidGenerator;

    private EventBusInterface $eventBus;

    private TermActionsChainInterface $actionsChain;

    public function __construct(
        TermWriteStorageInterface $storage,
        CurrentWebsiteInterface $currentWebsite,
        MetadataRepository $metadataRepository,
        UuidGeneratorInterface $uuidGenerator,
        EventBusInterface $eventBus,
        TermActionsChainInterface $actionsChain
    ) {
        $this->storage = $storage;
        $this->currentWebsite = $currentWebsite;
        $this->metadataRepository = $metadataRepository;
        $this->uuidGenerator = $uuidGenerator;
        $this->eventBus = $eventBus;
        $this->actionsChain = $actionsChain;
    }

    public function createNew(array $data): Term
    {
        return Term::buildFromArray(array_merge($data, [
            'id' => $this->uuidGenerator->generate(),
            'locale' => $this->currentWebsite->getLocale()->getCode(),
            'type' => 'category',
            'website_id' => $this->currentWebsite->getId(),
        ]));
    }

    public function find(string $id): Term
    {
        $term = $this->storage->find(
            $id,
            $this->currentWebsite->getLocale()->getCode(),
            $this->currentWebsite->getDefaultLocale()->getCode()
        );

        if (empty($term)) {
            throw new TermNotFoundException();
        }

        $term = Term::buildFromArray([
            'id'         => $term['id'],
            'type'       => $term['type'] ?? '',
            'locale'     => $term['locale'],
            'level'      => (int) $term['level'],
            'website_id' => $term['website_id'],
            'parent_id'  => $term['parent_id'],
            'name'       => $term['name'],
            'slug'       => $term['slug'],
            'visibility' => $term['visibility'] === '1',
            'metadata'   => $this->metadataRepository->findAll(TermMetadataEnum::TYPE, $id),
            'translated' => $term['translated'] ?? true,
        ]);

        $this->actionsChain->execute('find', $term);

        return $term;
    }

    public function insert(Term $term): void
    {
        $this->storage->beginTransaction();

        try {
            $this->actionsChain->execute('insert', $term);
            $this->storage->insert($this->extract($term), $this->currentWebsite->getDefaultLocale()->getCode());
            $this->metadataRepository->persist(
                TermMetadataEnum::TYPE,
                $term->getId()->getId(),
                $term->getAllMetadata()
            );
            $this->storage->commit();
        } catch (\Exception $exception) {
            $this->storage->rollback();
            throw $exception;
        }

        $this->eventBus->dispatchCollection($term->collectDomainEvents());
    }

    public function update(Term $term): void
    {
        $this->storage->beginTransaction();

        try {
            $this->actionsChain->execute('update', $term);
            $this->storage->update($this->extract($term), $this->currentWebsite->getDefaultLocale()->getCode());
            $this->metadataRepository->persist(
                TermMetadataEnum::TYPE,
                $term->getId()->getId(),
                $term->getAllMetadata()
            );
            $this->storage->commit();
        } catch (\Exception $exception) {
            $this->storage->rollback();
            throw $exception;
        }

        $this->eventBus->dispatchCollection(array_merge($term->collectDomainEvents(), [TermUpdated::fromTerm($term)]));
    }

    public function delete(Term $term): void
    {
        $this->storage->beginTransaction();

        try {
            $this->actionsChain->execute('delete', $term);
            $this->storage->delete($this->extract($term));
            $this->metadataRepository->delete(TermMetadataEnum::TYPE, $term->getId()->getId());
            $this->storage->commit();
        } catch (\Exception $exception) {
            $this->storage->rollback();
            throw $exception;
        }

        $this->eventBus->dispatch(TermDeleted::fromTerm($term));
    }

    private function extract(Term $term): array
    {
        return [
            'id'         => $term->getId()->getId(),
            'type'       => $term->getType(),
            'website_id' => $term->getWebsiteId(),
            'level'      => $term->getLevel(),
            'slug'       => $term->getSlug(),
            'name'       => $term->getName(),
            'visibility' => $term->getVisibility(),
            'parent_id'  => $term->getParentId(),
            'locale'     => $term->getLocale(),
        ];
    }
}

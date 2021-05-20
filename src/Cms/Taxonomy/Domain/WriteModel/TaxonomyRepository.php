<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\WriteModel;

use Tulia\Cms\Metadata\Domain\WriteModel\MetadataRepository;
use Tulia\Cms\Platform\Infrastructure\Bus\Event\EventBusInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Utils\Uuid\UuidGeneratorInterface;
use Tulia\Cms\Taxonomy\Domain\Metadata\TermMetadataEnum;
use Tulia\Cms\Taxonomy\Domain\TaxonomyType\RegistryInterface;
use Tulia\Cms\Taxonomy\Domain\TaxonomyType\TaxonomyTypeInterface;
use Tulia\Cms\Taxonomy\Domain\WriteModel\ActionsChain\TaxonomyActionsChainInterface;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\Taxonomy;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\Term;
use Tulia\Cms\Taxonomy\Ports\Infrastructure\Persistence\Domain\WriteModel\TermWriteStorageInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class TaxonomyRepository
{
    private RegistryInterface $taxonomyRegistry;

    private TermWriteStorageInterface $storage;

    private CurrentWebsiteInterface $currentWebsite;

    private MetadataRepository $metadataRepository;

    private UuidGeneratorInterface $uuidGenerator;

    private EventBusInterface $eventBus;

    private TaxonomyActionsChainInterface $actionsChain;

    public function __construct(
        RegistryInterface $taxonomyRegistry,
        TermWriteStorageInterface $storage,
        CurrentWebsiteInterface $currentWebsite,
        MetadataRepository $metadataRepository,
        UuidGeneratorInterface $uuidGenerator,
        EventBusInterface $eventBus,
        TaxonomyActionsChainInterface $actionsChain
    ) {
        $this->taxonomyRegistry = $taxonomyRegistry;
        $this->storage = $storage;
        $this->currentWebsite = $currentWebsite;
        $this->metadataRepository = $metadataRepository;
        $this->uuidGenerator = $uuidGenerator;
        $this->eventBus = $eventBus;
        $this->actionsChain = $actionsChain;
    }

    public function createNewTerm(array $data): Term
    {
        return Term::buildFromArray(array_merge($data, [
            'id' => $this->uuidGenerator->generate(),
            'locale' => $this->currentWebsite->getLocale()->getCode(),
            'type' => 'category',
            'website_id' => $this->currentWebsite->getId(),
        ]));
    }

    public function getTaxonomyType(string $type): TaxonomyTypeInterface
    {
        return $this->taxonomyRegistry->getType($type);
    }

    public function get(string $type): Taxonomy
    {
        $taxonomy = Taxonomy::buildFromArray([
            'type' => $this->getTaxonomyType($type),
            'terms' => $this->getTerms($type)
        ]);

        $this->actionsChain->execute('find', $taxonomy);

        return $taxonomy;
    }

    public function save(Taxonomy $taxonomy): void
    {
        $this->storage->beginTransaction();

        $this->actionsChain->execute('save', $taxonomy);

        try {
            foreach ($taxonomy->collectChangedTerms() as $change => $terms) {
                foreach ($terms as $term) {
                    if ($change === 'insert') {
                        $this->storage->insert(
                            $this->extractTerm($term),
                            $this->currentWebsite->getDefaultLocale()->getCode()
                        );
                        $this->metadataRepository->persist(
                            TermMetadataEnum::TYPE,
                            $term->getId()->getId(),
                            $term->getAllMetadata()
                        );
                    }

                    if ($change === 'update') {
                        $this->storage->update(
                            $this->extractTerm($term),
                            $this->currentWebsite->getDefaultLocale()->getCode()
                        );
                        $this->metadataRepository->persist(
                            TermMetadataEnum::TYPE,
                            $term->getId()->getId(),
                            $term->getAllMetadata()
                        );
                    }

                    if ($change === 'delete') {
                        $this->storage->delete($this->extractTerm($term));
                        $this->metadataRepository->delete(TermMetadataEnum::TYPE, $term->getId()->getId());
                    }
                }
            }

            $this->storage->commit();
        } catch (\Exception $exception) {
            $this->storage->rollback();
            throw $exception;
        }

        $taxonomy->clearTermsChangelog();

        $this->eventBus->dispatchCollection($term->collectDomainEvents());
    }

    private function getTerms(string $type): array
    {
        $terms = $this->storage->findByType(
            $type,
            $this->currentWebsite->getLocale()->getCode(),
            $this->currentWebsite->getDefaultLocale()->getCode()
        );

        $result = [];

        foreach ($terms as $term) {
            $result[] = Term::buildFromArray([
                'id'         => $term['id'],
                'type'       => $term['type'] ?? '',
                'locale'     => $term['locale'],
                'level'      => (int) $term['level'],
                'website_id' => $term['website_id'],
                'parent_id'  => $term['parent_id'],
                'name'       => $term['name'],
                'slug'       => $term['slug'],
                'path'       => $term['path'],
                'visibility' => $term['visibility'] === '1',
                'metadata'   => $this->metadataRepository->findAll(TermMetadataEnum::TYPE, $term['id']),
                'translated' => $term['translated'] ?? true,
            ]);
        }

        return $result;
    }

    private function extractTerm(Term $term): array
    {
        return [
            'id'         => $term->getId()->getId(),
            'type'       => $term->getType(),
            'website_id' => $term->getWebsiteId(),
            'level'      => $term->getLevel(),
            'slug'       => $term->getSlug(),
            'path'       => $term->getPath(),
            'name'       => $term->getName(),
            'visibility' => $term->isVisible(),
            'parent_id'  => $term->getParentId(),
            'locale'     => $term->getLocale(),
        ];
    }
}

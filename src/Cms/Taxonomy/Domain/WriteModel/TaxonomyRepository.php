<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\WriteModel;

use Tulia\Cms\ContentBuilder\Domain\TaxonomyType\Model\TaxonomyType;
use Tulia\Cms\ContentBuilder\Domain\TaxonomyType\Service\TaxonomyTypeRegistry;
use Tulia\Cms\Metadata\Domain\WriteModel\MetadataRepository;
use Tulia\Cms\Platform\Infrastructure\Bus\Event\EventBusInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Utils\Uuid\UuidGeneratorInterface;
use Tulia\Cms\Taxonomy\Domain\WriteModel\ActionsChain\TaxonomyActionsChainInterface;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Exception\TermNotFoundException;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\Taxonomy;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\Term;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\ValueObject\AttributeInfo;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\ValueObject\TermId;
use Tulia\Cms\Taxonomy\Ports\Infrastructure\Persistence\Domain\WriteModel\TermWriteStorageInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class TaxonomyRepository
{
    private const RESERVED_NAMES = ['title', 'slug', 'parent_id'];

    private TaxonomyTypeRegistry $taxonomyTypeRegistry;

    private TermWriteStorageInterface $storage;

    private CurrentWebsiteInterface $currentWebsite;

    private MetadataRepository $metadataRepository;

    private UuidGeneratorInterface $uuidGenerator;

    private EventBusInterface $eventBus;

    private TaxonomyActionsChainInterface $actionsChain;

    public function __construct(
        TermWriteStorageInterface $storage,
        CurrentWebsiteInterface $currentWebsite,
        MetadataRepository $metadataRepository,
        UuidGeneratorInterface $uuidGenerator,
        EventBusInterface $eventBus,
        TaxonomyActionsChainInterface $actionsChain,
        TaxonomyTypeRegistry $taxonomyTypeRegistry
    ) {
        $this->storage = $storage;
        $this->currentWebsite = $currentWebsite;
        $this->metadataRepository = $metadataRepository;
        $this->uuidGenerator = $uuidGenerator;
        $this->eventBus = $eventBus;
        $this->actionsChain = $actionsChain;
        $this->taxonomyTypeRegistry = $taxonomyTypeRegistry;
    }

    public function createNewTerm(Taxonomy $taxonomy): Term
    {
        return Term::createNew(
            $this->uuidGenerator->generate(),
            $taxonomy,
            $this->currentWebsite->getLocale()->getCode()
        );
    }

    public function getTaxonomyType(string $type): TaxonomyType
    {
        return $this->taxonomyTypeRegistry->get($type);
    }

    public function get(string $type): Taxonomy
    {
        $taxonomyType = $this->taxonomyTypeRegistry->get($type);
        $taxonomy = Taxonomy::create(
            $type,
            $this->currentWebsite->getId(),
            $this->getTerms($type)
        );

        try {
            $taxonomy->getTerm(new TermId(Term::ROOT_ID));
        } catch (TermNotFoundException $e) {
            $taxonomy->addTerm(Term::createRoot(
                $taxonomy,
                $this->currentWebsite->getLocale()->getCode()
            ));
        }

        foreach ($this->buildAttributesMapping($taxonomyType) as $name => $info) {
            $taxonomy->addAttributeInfo($name, new AttributeInfo(
                $info['is_multilingual'],
                $info['is_multiple'],
                $info['is_compilable'],
                $info['is_taxonomy'],
            ));
        }

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
                    $extracted = $this->extractTerm($taxonomy, $term);

                    if ($change === 'insert') {
                        $this->storage->insert(
                            $extracted,
                            $this->currentWebsite->getDefaultLocale()->getCode()
                        );
                        $this->metadataRepository->persist(
                            'term',
                            $term->getId()->getId(),
                            $extracted['attributes']
                        );
                    }

                    if ($change === 'update') {
                        $this->storage->update(
                            $extracted,
                            $this->currentWebsite->getDefaultLocale()->getCode()
                        );
                        $this->metadataRepository->persist(
                            'term',
                            $term->getId()->getId(),
                            $extracted['attributes']
                        );
                    }

                    if ($change === 'delete') {
                        $this->storage->delete($extracted);
                        $this->metadataRepository->delete('term', $term->getId()->getId());
                    }
                }
            }

            $this->storage->commit();
        } catch (\Exception $exception) {
            $this->storage->rollback();
            throw $exception;
        }

        $taxonomy->clearTermsChangelog();

        $this->eventBus->dispatchCollection($taxonomy->collectDomainEvents());
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
            $result[] = [
                'id'         => $term['id'],
                'locale'     => $term['locale'],
                'level'      => (int) $term['level'],
                'position'   => (int) $term['position'],
                'parent_id'  => $term['parent_id'],
                'title'      => $term['title'],
                'slug'       => $term['slug'],
                'path'       => $term['path'],
                'visibility' => $term['visibility'] === '1',
                'is_root'    => (bool) $term['is_root'],
                'metadata'   => $this->metadataRepository->findAll('term', $term['id']),
                'translated' => (bool) ($term['translated'] ?? false),
            ];
        }

        return $result;
    }

    private function extractTerm(Taxonomy $taxonomy, Term $term): array
    {
        $attributes = [];

        foreach ($term->getAttributes() as $name => $value) {
            if (\in_array($name, self::RESERVED_NAMES)) {
                continue;
            }

            $info = $taxonomy->getAttributeInfo($name);

            $attributes[$name] = [
                'value' => $value,
                'is_multilingual' => $info->isMultilingual(),
                'is_multiple' => $info->isMultiple(),
                'is_taxonomy' => $info->isTaxonomy(),
            ];
        }

        return [
            'id'         => $term->getId()->getId(),
            'website_id' => $term->getTaxonomy()->getWebsiteId(),
            'type'       => $term->getTaxonomy()->getType(),
            'level'      => $term->getLevel(),
            'position'   => $term->getPosition(),
            'slug'       => $term->getSlug(),
            'path'       => $term->getPath(),
            'title'      => $term->getTitle(),
            'visibility' => $term->isVisible(),
            'parent_id'  => $term->getParentId(),
            'locale'     => $term->getLocale(),
            'is_root'    => $term->isRoot(),
            'attributes' => $attributes,
        ];
    }

    private function buildAttributesMapping(TaxonomyType $taxonomyType): array
    {
        $result = [];

        foreach ($taxonomyType->getFields() as $field) {
            $result[$field->getName()] = [
                'is_multilingual' => $field->isMultilingual(),
                'is_multiple' => $field->isMultiple(),
                'is_compilable' => $field->hasFlag('compilable'),
                'is_taxonomy' => $field->getType() === 'taxonomy',
            ];
        }

        return $result;
    }
}

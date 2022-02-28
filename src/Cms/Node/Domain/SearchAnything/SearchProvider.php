<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\SearchAnything;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\Filemanager\Application\Service\ImageUrlResolver;
use Tulia\Cms\Filemanager\Domain\ReadModel\Finder\FileFinderInterface;
use Tulia\Cms\Filemanager\Domain\ReadModel\Finder\FileFinderScopeEnum as FilesScopeEnum;
use Tulia\Cms\Filemanager\Domain\WriteModel\FileTypeEnum;
use Tulia\Cms\Node\Domain\NodeFlag\NodeFlagRegistryInterface;
use Tulia\Cms\Node\Domain\ReadModel\Finder\NodeFinderInterface;
use Tulia\Cms\Node\Domain\ReadModel\Finder\NodeFinderScopeEnum as NodeScopeEnum;
use Tulia\Cms\Node\Domain\ReadModel\Model\Node;
use Tulia\Cms\SearchAnything\Model\Hit;
use Tulia\Cms\SearchAnything\Model\Results;
use Tulia\Cms\SearchAnything\Provider\AbstractProvider;
use Tulia\Cms\Shared\Domain\ReadModel\Finder\Model\Collection;

/**
 * @author Adam Banaszkiewicz
 */
class SearchProvider extends AbstractProvider
{
    private NodeFinderInterface $nodeFinder;
    private FileFinderInterface $filesFinder;
    private RouterInterface $router;
    private TranslatorInterface $translator;
    private ContentTypeRegistryInterface $contentTypeRegistry;
    private ImageUrlResolver $imageUrlResolver;
    private NodeFlagRegistryInterface $nodeFlagRegistry;

    public function __construct(
        NodeFinderInterface $nodeFinder,
        FileFinderInterface $filesFinder,
        RouterInterface $router,
        TranslatorInterface $translator,
        ContentTypeRegistryInterface $contentTypeRegistry,
        ImageUrlResolver $imageUrlResolver,
        NodeFlagRegistryInterface $nodeFlagRegistry
    ) {
        $this->nodeFinder  = $nodeFinder;
        $this->filesFinder = $filesFinder;
        $this->router = $router;
        $this->translator = $translator;
        $this->contentTypeRegistry = $contentTypeRegistry;
        $this->imageUrlResolver = $imageUrlResolver;
        $this->nodeFlagRegistry = $nodeFlagRegistry;
    }

    public function search(string $query, int $limit = 5, int $page = 1): Results
    {
        $results = new Results();

        $nodes = $this->nodeFinder->find([
            'search' => $query,
            'per_page' => $limit,
            'page' => $page,
        ], NodeScopeEnum::SEARCH);

        foreach ($nodes as $node) {
            $hit = new Hit($node->getTitle(), $this->router->generate('backend.node.edit', ['node_type' => $node->getType(), 'id' => $node->getId() ]));
            $hit->setDescription($node->getIntroduction());

            foreach ($node->getFlags() as $flag) {
                if ($this->nodeFlagRegistry->has($flag)) {
                    $hit->addTag(
                        $this->translator->trans($this->nodeFlagRegistry->get($flag)['label'], [], 'node'),
                        'fas fa-tag'
                    );
                }
            }

            $results->add($node->getId(), $hit);
        }

        $this->includeImages($nodes, $results);

        return $results;
    }

    public function getId(): string
    {
        return 'node';
    }

    public function getLabel(): array
    {
        return ['contents'];
    }

    public function getIcon(): string
    {
        return 'fas fa-clipboard';
    }

    private function includeImages(Collection $nodes, Results $results): void
    {
        $ids = [];

        /** @var Node $node */
        foreach ($nodes as $node) {
            if ($node->attribute('thumbnail')) {
                $ids[$node->getId()] = $node->attribute('thumbnail');
            }
        }

        if ($ids === []) {
            return;
        }

        $images = $this->filesFinder->find([
            'id__in' => $ids,
            'type'   => FileTypeEnum::IMAGE,
        ], FilesScopeEnum::SEARCH);

        if ($images->count() === 0) {
            return;
        }

        foreach ($results as $id => $hit) {
            foreach ($ids as $nodeId => $imageId) {
                if ($id === $nodeId) {
                    foreach ($images as $image) {
                        if ($image->getId() === $imageId) {
                            $hit->setImage($this->imageUrlResolver->thumbnail($image));
                        }
                    }
                }
            }
        }
    }
}

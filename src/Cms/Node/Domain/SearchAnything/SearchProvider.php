<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\SearchAnything;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Filemanager\Application\Service\ImageUrlResolver;
use Tulia\Cms\Filemanager\Domain\ReadModel\Finder\Enum\FilemanagerFinderScopeEnum as FilesScopeEnum;
use Tulia\Cms\Filemanager\Enum\TypeEnum;
use Tulia\Cms\Filemanager\Query\FinderFactoryInterface as FilemanagerFinderFactory;
use Tulia\Cms\Node\Domain\ReadModel\Finder\Enum\NodeFinderScopeEnum as NodeScopeEnum;
use Tulia\Cms\Node\Domain\NodeType\Enum\ParametersEnum;
use Tulia\Cms\Node\Domain\NodeType\RegistryInterface;
use Tulia\Cms\Node\Ports\Domain\ReadModel\NodeFinderInterface;
use Tulia\Cms\SearchAnything\Ports\Provider\AbstractProvider;
use Tulia\Cms\SearchAnything\Domain\Model\Hit;
use Tulia\Cms\SearchAnything\Domain\Model\Results;
use Tulia\Cms\Shared\Domain\ReadModel\Finder\Model\Collection;

/**
 * @author Adam Banaszkiewicz
 */
class SearchProvider extends AbstractProvider
{
    protected NodeFinderInterface $nodeFinder;

    protected FilemanagerFinderFactory $filesFinderFactory;

    protected RouterInterface $router;

    protected TranslatorInterface $translator;

    protected RegistryInterface $typesRegistry;

    protected ImageUrlResolver $imageUrlResolver;

    public function __construct(
        NodeFinderInterface $nodeFinder,
        FilemanagerFinderFactory $filesFinderFactory,
        RouterInterface $router,
        TranslatorInterface $translator,
        RegistryInterface $typesRegistry,
        ImageUrlResolver $imageUrlResolver
    ) {
        $this->nodeFinder  = $nodeFinder;
        $this->filesFinderFactory = $filesFinderFactory;
        $this->router = $router;
        $this->translator = $translator;
        $this->typesRegistry = $typesRegistry;
        $this->imageUrlResolver = $imageUrlResolver;
    }

    public function search(string $query, int $limit = 5, int $page = 1): Results
    {
        $results = new Results();

        $nodes = $this->nodeFinder->find([
            'search' => $query,
            'per_page' => $limit,
            'page' => $page,
            'count_found_rows' => true,
        ], NodeScopeEnum::SEARCH);

        foreach ($nodes as $node) {
            $hit = new Hit($node->getTitle(), $this->router->generate('backend.node.edit', ['node_type' => $node->getType(), 'id' => $node->getId() ]));
            $hit->setDescription($node->getIntroduction());

            $nodeType = $this->typesRegistry->getType($node->getType());
            $hit->addTag(
                $this->translator->trans('node', [], $nodeType->getTranslationDomain()),
                $nodeType->getParameter(ParametersEnum::ICON)
            );

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

        foreach ($nodes as $node) {
            if ($node->getMeta('thumbnail')) {
                $ids[$node->getId()] = $node->getMeta('thumbnail');
            }
        }

        $finder = $this->filesFinderFactory->getInstance(FilesScopeEnum::SEARCH);
        $finder->setCriteria([
            'id__in' => $ids,
            'type'   => TypeEnum::IMAGE,
        ]);
        $finder->fetchRaw();

        $images = $finder->getResult();

        if ($images->count() === 0) {
            return;
        }

        foreach ($results->getHits() as $hit) {
            foreach ($ids as $nodeId => $imageId) {
                if ($hit->getId() === $nodeId) {
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

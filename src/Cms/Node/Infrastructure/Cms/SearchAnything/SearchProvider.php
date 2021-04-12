<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Cms\SearchAnything;

use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Filemanager\Application\ImageUrlResolver;
use Tulia\Cms\Filemanager\Enum\TypeEnum;
use Tulia\Cms\Node\Infrastructure\NodeType\Enum\ParametersEnum;
use Tulia\Cms\Node\Query\Enum\ScopeEnum as NodeScopeEnum;
use Tulia\Cms\Node\Infrastructure\NodeType\RegistryInterface;
use Tulia\Cms\Node\Query\FinderFactoryInterface as NodeFinderFactory;
use Tulia\Cms\Filemanager\Query\FinderFactoryInterface as FilemanagerFinderFactory;
use Tulia\Cms\Filemanager\Enum\ScopeEnum as FilesScopeEnum;
use Tulia\Cms\Node\Query\Model\Collection;
use Tulia\Cms\SearchAnything\Provider\AbstractProvider;
use Tulia\Cms\SearchAnything\Results\Hit;
use Tulia\Cms\SearchAnything\Results\Results;
use Tulia\Cms\SearchAnything\Results\ResultsInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Adam Banaszkiewicz
 */
class SearchProvider extends AbstractProvider
{
    /**
     * @var NodeFinderFactory
     */
    protected $nodeFinderFactory;

    /**
     * @var FilemanagerFinderFactory
     */
    protected $filesFinderFactory;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var RegistryInterface
     */
    protected $typesRegistry;

    /**
     * @var ImageUrlResolver
     */
    protected $imageUrlResolver;

    /**
     * @param NodeFinderFactory $nodeFinderFactory
     * @param FilemanagerFinderFactory $filesFinderFactory
     * @param RouterInterface $router
     * @param TranslatorInterface $translator
     * @param RegistryInterface $typesRegistry
     * @param ImageUrlResolver $imageUrlResolver
     */
    public function __construct(
        NodeFinderFactory $nodeFinderFactory,
        FilemanagerFinderFactory $filesFinderFactory,
        RouterInterface $router,
        TranslatorInterface $translator,
        RegistryInterface $typesRegistry,
        ImageUrlResolver $imageUrlResolver
    ) {
        $this->nodeFinderFactory  = $nodeFinderFactory;
        $this->filesFinderFactory = $filesFinderFactory;
        $this->router             = $router;
        $this->translator         = $translator;
        $this->typesRegistry      = $typesRegistry;
        $this->imageUrlResolver   = $imageUrlResolver;
    }

    public function search(string $query, int $limit = 5, int $page = 1): ResultsInterface
    {
        $finder = $this->nodeFinderFactory->getInstance(NodeScopeEnum::SEARCH);
        $finder->setCriteria([
            'search'   => $query,
            'per_page' => $limit,
            'page'     => $page,
            'count_found_rows' => true,
        ]);
        $finder->fetchRaw();

        $results = new Results();

        $nodes = $finder->getResult();

        foreach ($nodes as $node) {
            $hit = new Hit($node->getTitle(), $this->router->generate('backend.node.edit', ['node_type' => $node->getType(), 'id' => $node->getId() ]));
            $hit->setId($node->getId());
            $hit->setDescription($node->getIntroduction());

            $nodeType = $this->typesRegistry->getType($node->getType());
            $hit->addTag(
                $this->translator->trans('node', [], $nodeType->getTranslationDomain()),
                $nodeType->getParameter(ParametersEnum::ICON)
            );

            $results->add($hit);
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

    private function includeImages(Collection $nodes, ResultsInterface $results): void
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

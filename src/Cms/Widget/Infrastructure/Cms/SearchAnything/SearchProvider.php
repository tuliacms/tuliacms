<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Infrastructure\Cms\SearchAnything;

use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Widget\Query\Enum\ScopeEnum;
use Tulia\Cms\Widget\Query\FinderFactoryInterface;
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
     * @var FinderFactoryInterface
     */
    protected $finderFactory;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @param FinderFactoryInterface $finderFactory
     * @param RouterInterface $router
     * @param TranslatorInterface $translator
     */
    public function __construct(
        FinderFactoryInterface $finderFactory,
        RouterInterface $router,
        TranslatorInterface $translator
    ) {
        $this->finderFactory = $finderFactory;
        $this->router             = $router;
        $this->translator         = $translator;
    }

    public function search(string $query, int $limit = 5, int $page = 1): ResultsInterface
    {
        $finder = $this->finderFactory->getInstance(ScopeEnum::SEARCH);
        $finder->setCriteria([
            'search'   => $query,
            'per_page' => $limit,
            'page'     => $page,
            'count_found_rows' => true,
        ]);
        $finder->fetchRaw();

        $results = new Results();

        $nodes = $finder->getResult();

        foreach ($nodes as $widget) {
            $hit = new Hit($widget->getName(), $this->router->generate('backend.widget.edit', ['id' => $widget->getId() ]));
            $hit->setId($widget->getId());

            $hit->addTag(
                $this->translator->trans('widgetSpaceIs', ['space' => $widget->getSpace()], 'widgets'),
                'fas fa-glass-whiskey'
            );

            $results->add($hit);
        }

        return $results;
    }

    public function getId(): string
    {
        return 'widget';
    }

    public function getLabel(): array
    {
        return ['widgets'];
    }

    public function getIcon(): string
    {
        return 'fas fa-cube';
    }
}

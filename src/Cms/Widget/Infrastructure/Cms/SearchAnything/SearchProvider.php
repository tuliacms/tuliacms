<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Infrastructure\Cms\SearchAnything;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\SearchAnything\Ports\Provider\AbstractProvider;
use Tulia\Cms\SearchAnything\Domain\Model\Hit;
use Tulia\Cms\SearchAnything\Domain\Model\Results;
use Tulia\Cms\Widget\Query\Enum\ScopeEnum;
use Tulia\Cms\Widget\Query\FinderFactoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class SearchProvider extends AbstractProvider
{
    protected FinderFactoryInterface $finderFactory;

    protected RouterInterface $router;

    protected TranslatorInterface $translator;

    public function __construct(
        FinderFactoryInterface $finderFactory,
        RouterInterface $router,
        TranslatorInterface $translator
    ) {
        $this->finderFactory = $finderFactory;
        $this->router             = $router;
        $this->translator         = $translator;
    }

    public function search(string $query, int $limit = 5, int $page = 1): Results
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

            $hit->addTag(
                $this->translator->trans('widgetSpaceIs', ['space' => $widget->getSpace()], 'widgets'),
                'fas fa-glass-whiskey'
            );

            $results->add($widget->getId(), $hit);
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

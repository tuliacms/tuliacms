<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Domain\SearchAnything;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\SearchAnything\Model\Hit;
use Tulia\Cms\SearchAnything\Model\Results;
use Tulia\Cms\SearchAnything\Provider\AbstractProvider;
use Tulia\Cms\Widget\Domain\ReadModel\Finder\WidgetFinderInterface;
use Tulia\Cms\Widget\Domain\ReadModel\Finder\WidgetFinderScopeEnum;

/**
 * @author Adam Banaszkiewicz
 */
class SearchProvider extends AbstractProvider
{
    private RouterInterface $router;

    private TranslatorInterface $translator;

    private WidgetFinderInterface $finder;

    public function __construct(
        RouterInterface $router,
        TranslatorInterface $translator,
        WidgetFinderInterface $finder
    ) {
        $this->router = $router;
        $this->translator = $translator;
        $this->finder = $finder;
    }

    public function search(string $query, int $limit = 5, int $page = 1): Results
    {
        $results = new Results();

        $widgets = $this->finder->find([
            'search'   => $query,
            'per_page' => $limit,
            'page'     => $page,
            'count_found_rows' => true,
        ], WidgetFinderScopeEnum::SEARCH);

        foreach ($widgets as $widget) {
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

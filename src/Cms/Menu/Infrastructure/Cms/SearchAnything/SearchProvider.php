<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Cms\SearchAnything;

use Tulia\Cms\Menu\Application\Query\Finder\Enum\ScopeEnum;
use Tulia\Cms\Menu\Application\Query\Finder\FinderFactoryInterface;
use Tulia\Cms\SearchAnything\Provider\AbstractProvider;
use Tulia\Cms\SearchAnything\Results\Hit;
use Tulia\Cms\SearchAnything\Results\Results;
use Tulia\Cms\SearchAnything\Results\ResultsInterface;
use Tulia\Component\Routing\RouterInterface;

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
     * @param FinderFactoryInterface $finderFactory
     * @param RouterInterface $router
     */
    public function __construct(
        FinderFactoryInterface $finderFactory,
        RouterInterface $router
    ) {
        $this->finderFactory = $finderFactory;
        $this->router = $router;
    }

    public function search(string $query, int $limit = 5, int $page = 1): ResultsInterface
    {
        $finder = $this->finderFactory->getInstance(ScopeEnum::INTERNAL);
        $finder->setCriteria([
            'search'   => $query,
            'per_page' => $limit,
            'page'     => $page,
        ]);
        $finder->fetchRaw();

        $results = new Results();

        foreach ($finder->getResult() as $menu) {
            $hit = new Hit($menu->getName(), $this->router->generate('backend.menu.item.edit', [
                'menuId' => $menu->getMenuId(),
                'id' => $menu->getId(),
            ]));
            $hit->setId($menu->getId());

            $results->add($hit);
        }

        return $results;
    }

    public function getId(): string
    {
        return 'menu';
    }

    public function getLabel(): array
    {
        return ['menu'];
    }

    public function getIcon(): string
    {
        return 'fas fa-bars';
    }
}

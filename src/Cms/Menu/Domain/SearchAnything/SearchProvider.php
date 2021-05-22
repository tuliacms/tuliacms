<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\SearchAnything;

use Tulia\Cms\Menu\Domain\ReadModel\Finder\Enum\ScopeEnum;
use Tulia\Cms\Menu\Ports\Infrastructure\Persistence\ReadModel\MenuFinderInterface;
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
    protected MenuFinderInterface $menuFinder;

    protected RouterInterface $router;

    public function __construct(
        MenuFinderInterface $menuFinder,
        RouterInterface $router
    ) {
        $this->menuFinder = $menuFinder;
        $this->router = $router;
    }

    public function search(string $query, int $limit = 5, int $page = 1): ResultsInterface
    {
        $menus = $this->menuFinder->find([
            'search'   => $query,
            'per_page' => $limit,
            'page'     => $page,
        ], ScopeEnum::INTERNAL);

        $results = new Results();

        foreach ($menus as $menu) {
            $hit = new Hit($menu->getName(), $this->router->generate('backend.menu.item.list', [
                'menuId' => $menu->getId(),
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

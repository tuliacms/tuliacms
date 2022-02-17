<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\SearchAnything;

use Symfony\Component\Routing\RouterInterface;
use Tulia\Cms\Menu\Domain\ReadModel\Finder\MenuFinderInterface;
use Tulia\Cms\Menu\Domain\ReadModel\Finder\MenuFinderScopeEnum;
use Tulia\Cms\SearchAnything\Model\Hit;
use Tulia\Cms\SearchAnything\Model\Results;
use Tulia\Cms\SearchAnything\Provider\AbstractProvider;

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

    public function search(string $query, int $limit = 5, int $page = 1): Results
    {
        $menus = $this->menuFinder->find([
            'search'   => $query,
            'per_page' => $limit,
            'page'     => $page,
        ], MenuFinderScopeEnum::INTERNAL);

        $results = new Results();

        foreach ($menus as $menu) {
            $hit = new Hit($menu->getName(), $this->router->generate('backend.menu.item.list', [
                'menuId' => $menu->getId(),
            ]));

            $results->add($menu->getId(), $hit);
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

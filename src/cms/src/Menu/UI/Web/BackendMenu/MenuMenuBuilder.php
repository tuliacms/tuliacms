<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\UI\Web\BackendMenu;

use Symfony\Component\HttpFoundation\RequestStack;
use Tulia\Cms\BackendMenu\Application\BuilderInterface;
use Tulia\Cms\BackendMenu\Application\Helper\BuilderHelperInterface;
use Tulia\Cms\BackendMenu\Application\Registry\ItemRegistryInterface;
use Tulia\Cms\Menu\Application\Query\Finder\Enum\ScopeEnum;
use Tulia\Cms\Menu\Application\Query\Finder\Exception\MultipleFetchException;
use Tulia\Cms\Menu\Application\Query\Finder\Exception\QueryException;
use Tulia\Cms\Menu\Application\Query\Finder\Exception\QueryNotFetchedException;
use Tulia\Cms\Menu\Application\Query\Finder\FinderFactoryInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class MenuMenuBuilder implements BuilderInterface
{
    private const SESSION_KEY = '_cms_app_backendmenu_menu_cache_%s';

    /**
     * @var BuilderHelperInterface
     */
    protected $helper;

    /**
     * @var FinderFactoryInterface
     */
    protected $finderFactory;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var CurrentWebsiteInterface
     */
    protected $currentWebsite;

    /**
     * @param BuilderHelperInterface $helper
     * @param FinderFactoryInterface $finderFactory
     * @param RequestStack $requestStack
     * @param CurrentWebsiteInterface $currentWebsite
     */
    public function __construct(
        BuilderHelperInterface $helper,
        FinderFactoryInterface $finderFactory,
        RequestStack $requestStack,
        CurrentWebsiteInterface $currentWebsite
    ) {
        $this->helper         = $helper;
        $this->finderFactory  = $finderFactory;
        $this->requestStack   = $requestStack;
        $this->currentWebsite = $currentWebsite;
    }

    /**
     * Event listener method, which clears cache when menu is created/updated/deleted.
     */
    public function clearCache(): void
    {
        $request = $this->requestStack->getMasterRequest();

        if ($request && $request->hasSession() && $request->getSession()->has($this->getCachekey())) {
            $request->getSession()->remove($this->getCachekey());
        }
    }

    /**
     * @param ItemRegistryInterface $registry
     *
     * @throws MultipleFetchException
     * @throws QueryException
     * @throws QueryNotFetchedException
     */
    public function build(ItemRegistryInterface $registry): void
    {
        $root = 'menu';

        $registry->add($root, [
            'label'    => $this->helper->trans('menu'),
            'link'     => '#',
            'icon'     => 'fas fa-bars',
            'priority' => 2000,
        ]);

        $registry->add($root . '_list', [
            'label'    => $this->helper->trans('menuList'),
            'link'     => $this->helper->generateUrl('backend.menu'),
            'parent'   => $root,
            'priority' => 5000,
        ]);

        foreach ($this->getMenus() as $menu) {
            $registry->add($root . '_menu_' . $menu['id'], [
                'label'    => $menu['name'],
                'link'     => $this->helper->generateUrl('backend.menu.item', ['menuId' => $menu['id']]),
                'parent'   => $root,
                'priority' => 3000,
            ]);
        }
    }

    /**
     * @return array
     *
     * @throws MultipleFetchException
     * @throws QueryException
     * @throws QueryNotFetchedException
     */
    private function getMenus(): array
    {
        $request = $this->requestStack->getMasterRequest();

        if ($request && $request->hasSession() && $request->getSession()->has($this->getCachekey())) {
            return $request->getSession()->get($this->getCachekey());
        }

        $finder = $this->finderFactory->getInstance(ScopeEnum::INTERNAL);
        $finder->fetchRaw();

        $menus = [];

        foreach ($finder->getResult() as $menu) {
            $menus[] = [
                'id'   => $menu->getId(),
                'name' => $menu->getName(),
            ];
        }

        if ($request && $request->hasSession()) {
            $request->getSession()->set($this->getCachekey(), $menus);
        }

        return $menus;
    }

    private function getCachekey(): string
    {
        return sprintf(self::SESSION_KEY, $this->currentWebsite->getId());
    }
}

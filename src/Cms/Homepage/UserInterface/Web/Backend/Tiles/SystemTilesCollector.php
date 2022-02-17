<?php

declare(strict_types=1);

namespace Tulia\Cms\Homepage\UserInterface\Web\Backend\Tiles;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class SystemTilesCollector implements DashboardTilesCollector
{
    protected RouterInterface $router;

    protected TranslatorInterface $translator;

    public function __construct(RouterInterface $router, TranslatorInterface $translator)
    {
        $this->router = $router;
        $this->translator = $translator;
    }

    public function collect(DashboardTilesCollection $collection): void
    {
        $collection
            ->add('users', [
                'name' => $this->translator->trans('users'),
                'link' => $this->router->generate('backend.user'),
                'icon' => 'fas fa-users',
            ])->add('websites', [
                'name' => $this->translator->trans('websites'),
                'link' => $this->router->generate('backend.website'),
                'icon' => 'fas fa-globe',
            ])
        ;
    }

    public function supports(string $group): bool
    {
        return $group === 'system';
    }
}

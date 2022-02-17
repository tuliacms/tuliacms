<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\Web\Backend\Tiles;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Homepage\UserInterface\Web\Backend\Tiles\DashboardTilesCollection;
use Tulia\Cms\Homepage\UserInterface\Web\Backend\Tiles\DashboardTilesCollector;

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
            ->add('content_model', [
                'name' => $this->translator->trans('contentModel', [], 'content_builder'),
                'link' => $this->router->generate('backend.content_builder.homepage'),
                'icon' => 'fas fa-box',
            ])
        ;
    }

    public function supports(string $group): bool
    {
        return $group === 'system';
    }
}

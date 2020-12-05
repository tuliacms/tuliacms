<?php

declare(strict_types=1);

namespace Tulia\Cms\Homepage\UI\Web\Tiles;

use Tulia\Cms\Dashboard\Tiles\Event\CollectTilesEvent;
use Tulia\Cms\Dashboard\Tiles\Tile;
use Tulia\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class SystemTiles
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router, TranslatorInterface $translator)
    {
        $this->router     = $router;
        $this->translator = $translator;
    }

    public function handle(CollectTilesEvent $event): void
    {
        if ($event->getGroup() === 'system') {
            $event->add(new Tile($this->translator->trans('users'), $this->router->generate('backend.user'), 'fas fa-users'));
            $event->add(new Tile($this->translator->trans('websites'), $this->router->generate('backend.website'), 'fas fa-globe'));
        }
    }
}

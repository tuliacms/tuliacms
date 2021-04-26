<?php

declare(strict_types=1);

namespace Tulia\Cms\Homepage\UserInterface\Web\Tiles;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Dashboard\Tiles\Event\CollectTilesEvent;
use Tulia\Cms\Dashboard\Tiles\Tile;

/**
 * @author Adam Banaszkiewicz
 */
class SystemTiles implements EventSubscriberInterface
{
    protected RouterInterface $router;
    protected TranslatorInterface $translator;

    public function __construct(RouterInterface $router, TranslatorInterface $translator)
    {
        $this->router = $router;
        $this->translator = $translator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CollectTilesEvent::class => 'handle',
        ];
    }

    public function handle(CollectTilesEvent $event): void
    {
        if ($event->getGroup() === 'system') {
            $event->add(new Tile($this->translator->trans('users'), $this->router->generate('backend.user'), 'fas fa-users'));
            $event->add(new Tile($this->translator->trans('websites'), $this->router->generate('backend.website'), 'fas fa-globe'));
        }
    }
}

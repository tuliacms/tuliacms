<?php

declare(strict_types=1);

namespace Tulia\Cms\BodyClass\Application\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tulia\Cms\BodyClass\Application\Event\CollectBodyClassEvent;
use Tulia\Component\Theme\Customizer\DetectorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class BodyClass implements EventSubscriberInterface
{
    private DetectorInterface $detector;

    public function __construct(DetectorInterface $detector)
    {
        $this->detector = $detector;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CollectBodyClassEvent::class => 'handle',
        ];
    }

    public function handle(CollectBodyClassEvent $event): void
    {
        $request = $event->getRequest();

        if ($request->attributes->get('_content_path') === '/') {
            $event->add('homepage');
        }

        if ($this->detector->isCustomizerMode()) {
            $event->add('customizer');
        }

        $event->add('locale-' . $request->attributes->get('_content_locale'));
    }
}

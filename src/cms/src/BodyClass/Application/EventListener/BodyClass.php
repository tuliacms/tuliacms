<?php

declare(strict_types=1);

namespace Tulia\Cms\BodyClass\Application\EventListener;

use Tulia\Cms\BodyClass\Application\Event\CollectBodyClassEvent;
use Tulia\Component\Theme\Customizer\DetectorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class BodyClass
{
    /**
     * @var DetectorInterface
     */
    private $detector;

    /**
     * @param DetectorInterface $detector
     */
    public function __construct(DetectorInterface $detector)
    {
        $this->detector = $detector;
    }

    /**
     * @param CollectBodyClassEvent $event
     */
    public function handle(CollectBodyClassEvent $event): void
    {
        $request = $event->getRequest();

        if ($request->getContentPath() === '/') {
            $event->add('homepage');
        }

        if ($this->detector->isCustomizerMode()) {
            $event->add('customizer');
        }

        $event->add('locale-' . $request->getContentLocale());
    }
}

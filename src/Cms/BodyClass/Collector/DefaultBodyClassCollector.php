<?php

declare(strict_types=1);

namespace Tulia\Cms\BodyClass\Collector;

use Symfony\Component\HttpFoundation\Request;
use Tulia\Component\Theme\Customizer\DetectorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DefaultBodyClassCollector implements BodyClassCollectorInterface
{
    private DetectorInterface $detector;

    public function __construct(DetectorInterface $detector)
    {
        $this->detector = $detector;
    }

    public function collect(Request $request, BodyClassCollection $collection): void
    {
        if ($request->attributes->get('_content_path') === '/') {
            $collection->add('is-homepage');
        }

        if ($this->detector->isCustomizerMode()) {
            $collection->add('is-customizer');
        }

        $collection->add('locale-' . $request->attributes->get('_content_locale'));
    }
}

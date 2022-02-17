<?php

declare(strict_types=1);

namespace Tulia\Cms\BodyClass\Framework\Twig\Extension;

use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\BodyClass\Collector\BodyClassService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class BodyClassExtension extends AbstractExtension
{
    private BodyClassService $bodyClassService;

    public function __construct(BodyClassService $bodyClassService)
    {
        $this->bodyClassService = $bodyClassService;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('body_class', function (Request $request, array $append = []) {
                $collection = $this->bodyClassService->collect($request);
                $collection->add(...$append);

                return implode(' ', $collection->getAll());
            }, [
                'is_safe' => [ 'html' ]
            ]),
        ];
    }
}

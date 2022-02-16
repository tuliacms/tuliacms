<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer;

use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @author Adam Banaszkiewicz
 */
class RequestStackDetector implements DetectorInterface
{
    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function isCustomizerMode(): bool
    {
        $request = $this->requestStack->getMainRequest();

        if (! $request) {
            return false;
        }

        return $request->query->get('mode') === 'customizer';
    }

    public function getChangesetId(): string
    {
        $request = $this->requestStack->getMainRequest();

        if (! $request) {
            return '';
        }

        return (string) $request->query->get('changeset', '');
    }
}

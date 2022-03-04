<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer;

use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @author Adam Banaszkiewicz
 */
class RequestStackDetector implements DetectorInterface
{
    private RequestStack $requestStack;
    private ?bool $isCustomizerMode = null;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function isCustomizerMode(): bool
    {
        if ($this->isCustomizerMode !== null) {
            return $this->isCustomizerMode;
        }

        $request = $this->requestStack->getMainRequest();

        if (! $request) {
            return $this->isCustomizerMode = false;
        }

        return $this->isCustomizerMode = $request->query->get('mode') === 'customizer';
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

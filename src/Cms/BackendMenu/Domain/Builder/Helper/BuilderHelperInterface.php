<?php

declare(strict_types=1);

namespace Tulia\Cms\BackendMenu\Domain\Builder\Helper;

use Symfony\Component\Routing\RouterInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface BuilderHelperInterface
{
    public function isHomepage(): bool;

    public function isInPath(string $path): bool;

    public function generateUrl(string $route, array $parameters = [], int $referenceType = RouterInterface::ABSOLUTE_PATH): string;

    public function trans($id, array $parameters = [], $domain = null, $locale = null): string;
}

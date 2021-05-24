<?php

declare(strict_types=1);

namespace Tulia\Cms\BackendMenu\Domain\Builder\Helper;

use Symfony\Component\Routing\RouterInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface BuilderHelperInterface
{
    /**
     * @return bool
     */
    public function isHomepage(): bool;

    /**
     * @param string $path
     *
     * @return bool
     */
    public function isInPath(string $path): bool;

    /**
     * @param string $route
     * @param array  $parameters
     * @param int    $referenceType
     *
     * @return string
     */
    public function generateUrl(string $route, array $parameters = [], int $referenceType = RouterInterface::TYPE_PATH): string;

    /**
     * @param       $id
     * @param array $parameters
     * @param null  $domain
     * @param null  $locale
     *
     * @return string
     */
    public function trans($id, array $parameters = [], $domain = null, $locale = null): string;
}

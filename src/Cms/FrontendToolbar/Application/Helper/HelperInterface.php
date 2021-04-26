<?php

declare(strict_types=1);

namespace Tulia\Cms\FrontendToolbar\Application\Helper;

use Symfony\Component\Routing\RouterInterface;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface HelperInterface
{
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

    /**
     * @param ViewInterface $view
     *
     * @return string
     */
    public function render(ViewInterface $view): string;
}

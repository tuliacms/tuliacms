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
    public function generateUrl(string $route, array $parameters = [], int $referenceType = RouterInterface::ABSOLUTE_PATH): string;
    public function trans($id, array $parameters = [], $domain = null, $locale = null): string;
    public function render(ViewInterface $view): string;
}

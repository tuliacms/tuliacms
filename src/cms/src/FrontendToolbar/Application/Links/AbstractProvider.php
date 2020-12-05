<?php

declare(strict_types=1);

namespace Tulia\Cms\FrontendToolbar\Application\Links;

use Tulia\Framework\Http\Request;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractProvider implements ProviderInterface
{
    public function provideLinks(Links $links, Request $request): void
    {

    }

    public function provideContent(Request $request): string
    {
        return '';
    }
}

<?php

declare(strict_types=1);

namespace Tulia\Cms\Breadcrumbs\Application;

use Tulia\Cms\Platform\Shared\Breadcrumbs\BreadcrumbsInterface;
use Tulia\Framework\Http\Request;

/**
 * @author Adam Banaszkiewicz
 */
interface GeneratorInterface
{
    public function generateFromRequest(Request $request): BreadcrumbsInterface;

    public function generateFromIdentity(object $identity): BreadcrumbsInterface;
}

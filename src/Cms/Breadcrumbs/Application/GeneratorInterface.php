<?php

declare(strict_types=1);

namespace Tulia\Cms\Breadcrumbs\Application;

use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Platform\Shared\Breadcrumbs\BreadcrumbsInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface GeneratorInterface
{
    public function generateFromRequest(Request $request): BreadcrumbsInterface;

    public function generateFromIdentity(object $identity): BreadcrumbsInterface;
}

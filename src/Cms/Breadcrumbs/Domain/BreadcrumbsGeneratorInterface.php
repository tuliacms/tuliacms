<?php

declare(strict_types=1);

namespace Tulia\Cms\Breadcrumbs\Domain;

use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Platform\Shared\Breadcrumbs\BreadcrumbsInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface BreadcrumbsGeneratorInterface
{
    public function generateFromRequest(Request $request): BreadcrumbsInterface;

    public function generateFromIdentity(object $identity): BreadcrumbsInterface;
}

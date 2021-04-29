<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\ReadModel\Factory;

use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Website\Domain\ReadModel\Model\Website;

/**
 * @author Adam Banaszkiewicz
 */
interface WebsiteFactoryInterface
{
    /**
     * @param array $data
     *
     * @return Website
     */
    public function createNew(array $data = []): Website;

    /**
     * @param Request $request
     * @param array $data
     *
     * @return Website
     */
    public function createNewFromRequest(Request $request, array $data = []): Website;
}

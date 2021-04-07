<?php

declare(strict_types=1);

namespace Tulia\Framework\Kernel\Controller;

use Tulia\Framework\Http\Request;
use Tulia\Framework\Kernel\Exception\ArgumentNotResolvedException;

/**
 * @author Adam Banaszkiewicz
 */
interface ArgumentResolverInterface
{
    /**
     * @param Request $request
     * @param $class
     * @param string $method
     * @return array
     * @throws ArgumentNotResolvedException
     */
    public function getArguments(Request $request, $class, string $method): array;
}

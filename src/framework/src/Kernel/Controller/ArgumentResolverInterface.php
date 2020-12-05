<?php

declare(strict_types=1);

namespace Tulia\Framework\Kernel\Controller;

use Tulia\Framework\Http\Request;

/**
 * @author Adam Banaszkiewicz
 */
interface ArgumentResolverInterface
{
    /**
     * @param Request $request
     *
     * @param         $class
     * @param string  $method
     *
     * @return array
     */
    public function getArguments(Request $request, $class, string $method): array;
}

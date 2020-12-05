<?php

declare(strict_types=1);

namespace Tulia\Framework\Kernel\Controller;

use Tulia\Framework\Http\Request;
use Tulia\Framework\Kernel\Exception\ControllerNotCallableException;

/**
 * @author Adam Banaszkiewicz
 */
interface ControllerResolverInterface
{
    /**
     * @param Request $request
     *
     * @return callable|null
     *
     * @throws ControllerNotCallableException
     */
    public function getController(Request $request): ?callable;
}

<?php

declare(strict_types=1);

namespace Tulia\Component\DependencyInjection\Exception;

use Psr\Container\NotFoundExceptionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class MissingServiceException extends DependencyInjectionException implements NotFoundExceptionInterface
{

}

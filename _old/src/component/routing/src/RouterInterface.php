<?php

declare(strict_types=1);

namespace Tulia\Component\Routing;

use Tulia\Component\Routing\Request\RequestContextInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface RouterInterface
{
    public const TYPE_URL  = 1;
    public const TYPE_PATH = 2;

    public function generate(string $name, array $params = [], int $type = RouterInterface::TYPE_PATH, ?RequestContextInterface $context = null): string;
    public function path(string $path): string;
    public function url(string $path): string;
    public function match(string $pathinfo, ?RequestContextInterface $context = null): ?array;
    public function getRequestContext(): RequestContextInterface;
}

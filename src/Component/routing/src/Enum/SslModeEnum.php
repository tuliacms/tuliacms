<?php

declare(strict_types=1);

namespace Tulia\Component\Routing\Enum;

/**
 * @author Adam Banaszkiewicz
 */
class SslModeEnum
{
    public const ALLOWED_BOTH  = 'ALLOWED_BOTH';
    public const FORCE_SSL     = 'FORCE_SSL';
    public const FORCE_NON_SSL = 'FORCE_NON_SSL';
}

<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Infrastructure\Utils\Uuid;

use Ramsey\Uuid\Uuid;

/**
 * @author Adam Banaszkiewicz
 */
class RamseyUuidGenerator implements UuidGeneratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function generate(): string
    {
        return (string) Uuid::uuid4();
    }
}

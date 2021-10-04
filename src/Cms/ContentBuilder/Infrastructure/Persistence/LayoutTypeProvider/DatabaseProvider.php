<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Infrastructure\Persistence\LayoutTypeProvider;

use Tulia\Cms\ContentBuilder\Domain\LayoutType\Service\AbstractLayoutTypeProviderProvider;
use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\Connection;

/**
 * @author Adam Banaszkiewicz
 */
class DatabaseProvider extends AbstractLayoutTypeProviderProvider
{
    private Connection $connection;

    /*public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }*/

    public function provide(): array
    {
        return [];
    }
}

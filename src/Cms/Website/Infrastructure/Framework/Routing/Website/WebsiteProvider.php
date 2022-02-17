<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Infrastructure\Framework\Routing\Website;

use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\ConnectionInterface;
use Tulia\Component\Routing\Website\WebsiteProviderInterface;

/**
 * @author Adam Banaszkiewicz
 */
class WebsiteProvider implements WebsiteProviderInterface
{
    private ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function provide(): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT * FROM #__website AS w
            INNER JOIN #__website_locale AS wl
            ON w.id = wl.website_id'
        );
    }
}

<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Infrastructure\Persistence\ContentProvider;

use Tulia\Cms\ContentBuilder\Domain\ReadModel\Service\AbstractContentTypeProvider;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ContentTypeDatabaseProvider extends AbstractContentTypeProvider
{
    use LayoutTypeDatabaseProviderTrait;
    use ContentTypeDatabaseProviderTrait;

    private ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function provide(): array
    {
        $result = [];
        dump($this->getTypes());

        foreach ($this->getTypes() as $type) {
            $result[] = $this->buildFromArray($type);
        }

        return $result;
    }
}

<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Infrastructure\Persistence\ContentProvider;

use Tulia\Cms\ContentBuilder\Domain\TaxonomyType\Model\TaxonomyType;
use Tulia\Cms\ContentBuilder\Domain\TaxonomyType\Service\AbstractTaxonomyTypeProvider;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class TaxonomyTypeDatabaseProvider extends AbstractTaxonomyTypeProvider
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

        foreach ($this->getTypes('taxonomy') as $type) {
            $result[] = $this->buildTaxonomyType(
                $type['code'],
                $type,
                $this->buildLayoutType($this->getLayoutType($type['layout']))
            );
        }

        return $result;
    }
}

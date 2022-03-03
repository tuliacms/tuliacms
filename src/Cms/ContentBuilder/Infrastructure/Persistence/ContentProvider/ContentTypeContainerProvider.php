<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Infrastructure\Persistence\ContentProvider;

use Tulia\Cms\ContentBuilder\Domain\ReadModel\Service\AbstractContentTypeProvider;

/**
 * @author Adam Banaszkiewicz
 */
class ContentTypeContainerProvider extends AbstractContentTypeProvider
{
    use SymfonyContainerStandarizableTrait;

    private array $configuration;

    public function __construct(array $configuration)
    {
        $this->configuration = $configuration;
    }

    public function provide(): array
    {
        $result = [];

        foreach ($this->configuration as $code => $type) {
            $type['code'] = $code;
            $type['internal'] = true;
            $type = $this->standarizeArray($type);

            $result[] = $this->buildFromArray($type);
        }

        return $result;
    }
}

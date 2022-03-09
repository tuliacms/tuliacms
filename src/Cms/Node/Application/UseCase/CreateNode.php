<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Application\UseCase;

use Tulia\Cms\Attributes\Domain\WriteModel\Model\Attribute;
use Tulia\Cms\User\Application\UseCase\AbstractNodeUseCase;

/**
 * @author Adam Banaszkiewicz
 */
class CreateNode extends AbstractNodeUseCase
{
    /**
     * @param Attribute[] $attributes
     */
    public function __invoke(string $nodeType, array $attributes): void
    {
        $node = $this->repository->createNew($nodeType);

        $this->updateModel($node, $attributes);
        $this->create($node);
    }
}

<?php

declare(strict_types=1);

namespace Tulia\Cms\EditLinks\Ports\Domain;

use Tulia\Cms\EditLinks\Domain\Collection;

/**
 * @author Adam Banaszkiewicz
 */
interface EditLinksCollectorInterface
{
    public function collect(Collection $collection, object $object, array $options = []): void;

    public function supports(object $object): bool;
}

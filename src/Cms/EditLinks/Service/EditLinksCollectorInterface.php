<?php

declare(strict_types=1);

namespace Tulia\Cms\EditLinks\Service;

use Tulia\Cms\EditLinks\Model\Collection;

/**
 * @author Adam Banaszkiewicz
 */
interface EditLinksCollectorInterface
{
    public function collect(Collection $collection, object $object, array $options = []): void;

    public function supports(object $object): bool;
}

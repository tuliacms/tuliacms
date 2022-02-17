<?php

declare(strict_types=1);

namespace Tulia\Cms\BackendMenu\Domain\Builder;

/**
 * @author Adam Banaszkiewicz
 */
interface HtmlBuilderInterface
{
    public function build(array $params = []): string;
}

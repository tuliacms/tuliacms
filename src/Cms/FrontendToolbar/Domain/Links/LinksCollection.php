<?php

declare(strict_types=1);

namespace Tulia\Cms\FrontendToolbar\Domain\Links;

/**
 * @author Adam Banaszkiewicz
 */
class LinksCollection
{
    protected array $links = [];

    public function add(string $name, Link $link): void
    {
        $this->links[$name] = $link;
    }

    /**
     * @return Link[]
     */
    public function all(): array
    {
        return $this->links;
    }
}

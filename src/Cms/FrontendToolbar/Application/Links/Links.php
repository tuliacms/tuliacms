<?php

declare(strict_types=1);

namespace Tulia\Cms\FrontendToolbar\Application\Links;

/**
 * @author Adam Banaszkiewicz
 */
class Links
{
    /**
     * @var array
     */
    protected $links = [];

    /**
     * @param string $name
     * @param Link $link
     */
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

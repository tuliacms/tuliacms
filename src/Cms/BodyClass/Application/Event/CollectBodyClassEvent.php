<?php

declare(strict_types=1);

namespace Tulia\Cms\BodyClass\Application\Event;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @author Adam Banaszkiewicz
 */
class CollectBodyClassEvent extends Event
{
    protected Request $request;
    protected array $classes = [];

    public function __construct(Request $request, array $classes = [])
    {
        $this->request = $request;
        $this->classes = $classes;
    }

    /**
     * @param array $classes
     */
    public function add(...$classes): void
    {
        foreach ($classes as $name) {
            $this->classes[$name] = $name;
        }
    }

    /**
     * @param string $name
     */
    public function remove(string $name): void
    {
        unset($this->classes[$name]);
    }

    /**
     * @return array
     */
    public function getAll(): array
    {
        return $this->classes;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }
}
<?php

declare(strict_types=1);

namespace Tulia\Cms\EditLinks\Application\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * @author Adam Banaszkiewicz
 */
class CollectEditLinksEvent extends Event
{
    /**
     * @var array
     */
    protected $links = [];

    /**
     * @var array
     */
    protected $parameters = [];

    /**
     * @var object
     */
    protected $object;

    /**
     * @param $object
     * @param array $parameters
     */
    public function __construct($object, array $parameters = [])
    {
        $this->object     = $object;
        $this->parameters = $parameters;
    }

    /**
     * @return object
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function getParameter(string $name)
    {
        return $this->parameters[$name] ?? null;
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @param string $name
     * @param array $link
     */
    public function add(string $name, array $link): void
    {
        $this->links[$name] = array_merge([
            'label'    => '',
            'link'     => '',
            'priority' => 0,
        ], $link);
    }

    /**
     * @param string $name
     */
    public function remove(string $name): void
    {
        unset($this->links[$name]);
    }

    /**
     * @return array
     */
    public function getAll(): array
    {
        return $this->links;
    }
}

<?php

declare(strict_types=1);

namespace Tulia\Component\Routing;

/**
 * @author Adam Banaszkiewicz
 */
class Route
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $path;

    /**
     * @var array
     */
    private $parts = [];

    /**
     * @var array
     */
    private $defaults = [];

    /**
     * @var string
     */
    private $controller;

    /**
     * @var array
     */
    private $methods = [];

    /**
     * @var string
     */
    private $group = '';

    /**
     * @var string
     */
    private $suffix = '';

    public function __construct(string $name, string $path, string $controller, array $methods = [])
    {
        $this->name = $name;
        $this->path = $path;
        $this->controller = $controller;
        $this->methods = $methods;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     * @param string $group
     *
     * @return array
     */
    public function getParts(string $group): array
    {
        return $this->parts[$group] ?? [];
    }

    /**
     * @param string $group
     * @param array $parts
     */
    public function setParts(string $group, array $parts): void
    {
        $this->parts[$group] = $parts;
    }

    /**
     * @return array
     */
    public function getDefaults(): array
    {
        return $this->defaults;
    }

    /**
     * @param array $defaults
     */
    public function setDefaults(array $defaults): void
    {
        $this->defaults = $defaults;
    }

    /**
     * @return string
     */
    public function getController(): string
    {
        return $this->controller;
    }

    /**
     * @param string $controller
     */
    public function setController(string $controller): void
    {
        $this->controller = $controller;
    }

    /**
     * @return array
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * @param array $methods
     */
    public function setMethods(array $methods): void
    {
        $this->methods = $methods;
    }

    /**
     * @return string
     */
    public function getGroup(): string
    {
        return $this->group;
    }

    /**
     * @param string $group
     */
    public function setGroup(string $group): void
    {
        $this->group = $group;
    }

    /**
     * @return string
     */
    public function getSuffix(): string
    {
        return $this->suffix;
    }

    /**
     * @param string $suffix
     */
    public function setSuffix(string $suffix): void
    {
        $this->suffix = $suffix;
    }
}

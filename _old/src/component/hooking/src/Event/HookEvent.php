<?php

namespace Tulia\Component\Hooking\Event;

use Symfony\Contracts\EventDispatcher\Event;

abstract class HookEvent extends Event
{
    protected $name;
    protected $arguments = [];
    protected $content;

    public function __construct(string $name, array $arguments = [], $content = null)
    {
        $this->name = $name;
        $this->setAll($arguments);
        $this->setContent($content);
    }

    abstract public function getType(): string;

    public function getName(): string
    {
        return $this->name;
    }

    public function setContent($content): HookEvent
    {
        $this->content = $content;

        return $this;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setAll(array $arguments): HookEvent
    {
        $this->arguments = $arguments;

        return $this;
    }

    public function getAll(): array
    {
        return $this->arguments;
    }

    public function set(string $name, $value)
    {
        $this->arguments[$name] = $arguments;

        return $this;
    }

    public function get(string $name, $default = null)
    {
        return isset($this->arguments[$name]) ? $this->arguments[$name] : $default;
    }

    public function has(string $name): bool
    {
        return array_key_exists($name, $this->arguments);
    }
}

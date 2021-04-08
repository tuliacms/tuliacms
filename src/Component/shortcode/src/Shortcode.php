<?php

declare(strict_types=1);

namespace Tulia\Component\Shortcode;

/**
 * @author Adam Banaszkiewicz
 */
class Shortcode implements ShortcodeInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $parameters = [];

    /**
     * @var string|null
     */
    protected $content;

    /**
     * @param string $name
     * @param array  $parameters
     * @param null   $content
     */
    public function __construct(string $name, array $parameters = [], $content = null)
    {
        $this->name       = $name;
        $this->parameters = $parameters;
        $this->content    = $content;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function hasParameters(): bool
    {
        return $this->parameters !== [];
    }

    /**
     * {@inheritdoc}
     */
    public function getParameter(string $name, $default = null)
    {
        return $this->parameters[$name] ?? $default;
    }

    /**
     * {@inheritdoc}
     */
    public function getArrayParameter(string $name, string $separator = ','): array
    {
        if (isset($this->parameters[$name]) === false) {
            return [];
        }

        $result = explode($separator, $this->parameters[$name]);
        $result = array_map(function ($val) {
            return trim($val);
        }, $result);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function hasParameter(string $name): bool
    {
        return \array_key_exists($name, $this->parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * {@inheritdoc}
     */
    public function hasContent(): bool
    {
        return (bool) $this->content;
    }
}

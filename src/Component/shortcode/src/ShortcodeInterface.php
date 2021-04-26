<?php

declare(strict_types=1);

namespace Tulia\Component\Shortcode;

/**
 * @author Adam Banaszkiewicz
 */
interface ShortcodeInterface
{
    public const OPEN_TAG_PATTERN  = '/[\s+]?<[^>]*>[\s+]?$/i';
    public const CLOSE_TAG_PATTERN = '/^[\s+]?<\/([a-z0-9\s]+)>[\s+]?/i';

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return array
     */
    public function getParameters(): array;

    /**
     * @return bool
     */
    public function hasParameters(): bool;

    /**
     * @param string $name
     * @param null   $default
     *
     * @return mixed
     */
    public function getParameter(string $name, $default = null);

    /**
     * @param string $name
     * @param string $separator
     *
     * @return mixed
     */
    public function getArrayParameter(string $name, string $separator = ','): array;

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasParameter(string $name): bool;

    /**
     * @return string|null
     */
    public function getContent(): ?string;

    /**
     * @return bool
     */
    public function hasContent(): bool;
}

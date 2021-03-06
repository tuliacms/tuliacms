<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Ports\Infrastructure\Utils\Slug;

/**
 * @author Adam Banaszkiewicz
 */
interface SluggerInterface
{
    /**
     * @param $input
     * @param string $separator
     * @param string|null $locale
     *
     * @return string|null
     */
    public function url($input, string $separator = '-', string $locale = null): ?string;

    /**
     * @param $input
     * @param string $separator
     *
     * @return string|null
     */
    public function filename($input, string $separator = '-'): ?string;
}

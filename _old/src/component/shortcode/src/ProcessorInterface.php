<?php

declare(strict_types=1);

namespace Tulia\Component\Shortcode;

/**
 * @author Adam Banaszkiewicz
 */
interface ProcessorInterface
{
    /**
     * @param string $input
     *
     * @return string
     */
    public function process(string $input): string;
}

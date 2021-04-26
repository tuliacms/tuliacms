<?php

declare(strict_types=1);

namespace Tulia\Component\Shortcode\Compiler;

use Tulia\Component\Shortcode\ShortcodeInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface ShortcodeCompilerInterface
{
    /**
     * @param ShortcodeInterface $shortcode
     *
     * @return string
     */
    public function compile(ShortcodeInterface $shortcode): string;

    /**
     * @return string
     */
    public function getName(): string;
}

<?php

declare(strict_types=1);

namespace Tulia\Component\Shortcode\Compiler;

use Tulia\Component\Shortcode\ShortcodeInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface ShortcodeCompilerInterface
{
    public function compile(ShortcodeInterface $shortcode): string;

    public function getAlias(): string;
}

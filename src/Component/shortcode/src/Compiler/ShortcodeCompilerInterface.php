<?php

declare(strict_types=1);

namespace Tulia\Component\Shortcode\Compiler;

use Tulia\Cms\ContactForms\Domain\FieldsParser\Exception\InvalidFieldNameException;
use Tulia\Component\Shortcode\ShortcodeInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface ShortcodeCompilerInterface
{
    /**
     * @throws InvalidFieldNameException
     */
    public function compile(ShortcodeInterface $shortcode): string;

    public function getAlias(): string;
}

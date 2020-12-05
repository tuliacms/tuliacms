<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Application\FieldType\Parser;

use Tulia\Component\Shortcode\ShortcodeInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface FieldParserInterface
{
    public function getName(): string;
    public function parseShortcode(ShortcodeInterface $shortcode): array;
    public function getDefinition(): array;
}

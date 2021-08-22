<?php

declare(strict_types=1);

namespace Tulia\Cms\WysiwygEditor\Application;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractWysiwygEditor implements WysiwygEditorInterface
{
    abstract public function getId(): string;

    abstract public function getName(): string;

    abstract public function render(string $name, ?string $content = null, array $params = []): string;
}

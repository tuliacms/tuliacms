<?php

declare(strict_types=1);

namespace Tulia\Cms\WysiwygEditor\Application;

/**
 * @author Adam Banaszkiewicz
 */
interface WysiwygEditorInterface
{
    public function getId(): string;

    public function getName(): string;

    public function render(string $name, ?string $content = null, array $params = []): string;
}

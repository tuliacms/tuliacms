<?php

declare(strict_types=1);

namespace Tulia\Cms\WysiwygEditor\Core\Application;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractWysiwygEditor implements WysiwygEditorInterface
{
    /**
     * {@inheritdoc}
     */
    abstract public function getId(): string;

    /**
     * {@inheritdoc}
     */
    abstract public function getName(): string;

    /**
     * {@inheritdoc}
     */
    abstract public function render(string $name, ?string $content = null, array $params = []): string;
}

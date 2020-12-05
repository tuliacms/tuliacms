<?php

declare(strict_types=1);

namespace Tulia\Cms\WysiwygEditor\Application;

/**
 * @author Adam Banaszkiewicz
 */
interface WysiwygEditorInterface
{
    /**
     * @return string
     */
    public function getId(): string;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $name
     * @param string $content
     * @param array $params
     *
     * @return string
     */
    public function render(string $name, ?string $content, array $params = []): string;
}

<?php

declare(strict_types=1);

namespace Tulia\Cms\WysiwygEditor\Core\Application;

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
     * @param null|string $content
     * @param array $params
     *
     * @return string
     */
    public function render(string $name, ?string $content = null, array $params = []): string;
}

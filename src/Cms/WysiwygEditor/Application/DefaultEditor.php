<?php

declare(strict_types=1);

namespace Tulia\Cms\WysiwygEditor\Application;

/**
 * @author Adam Banaszkiewicz
 */
class DefaultEditor extends AbstractWysiwygEditor
{
    public function getId(): string
    {
        return 'internal';
    }

    public function getName(): string
    {
        return 'Internal';
    }

    public function render(string $name, ?string $content = null, array $params = []): string
    {
        return '<textarea name="' . $name . '" id="' . ($params['id'] ?? uniqid('default-wysiwyg-editor-', true)) . '" class="form-control" style="height:300px;">' . $content . '</textarea>';
    }
}

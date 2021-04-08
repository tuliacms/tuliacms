<?php

declare(strict_types=1);

namespace Tulia\Cms\WysiwygEditor\Core\Application;

/**
 * @author Adam Banaszkiewicz
 */
class DefaultEditor extends AbstractWysiwygEditor
{
    /**
     * {@inheritdoc}
     */
    public function getId(): string
    {
        return 'internal';
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'Internal';
    }

    /**
     * {@inheritdoc}
     */
    public function render(string $name, ?string $content = null, array $params = []): string
    {
        return '<textarea name="' . $name . '" id="' . ($params['id'] ?? uniqid('default-wysiwyg-editor-', true)) . '" class="form-control" style="height:300px;">' . $content . '</textarea>';
    }
}

<?php

declare(strict_types=1);

namespace Tulia\Cms\WysiwygEditor\Application;

/**
 * @author Adam Banaszkiewicz
 */
class Registry implements RegistryInterface
{
    protected iterable $editors = [];

    protected ?string $active = null;

    public function __construct(iterable $editors, ?string $active = null)
    {
        $this->editors = $editors;
        $this->active  = $active;
    }

    public function getActiveEditor(): WysiwygEditorInterface
    {
        foreach ($this->editors as $editor) {
            if ($editor->getId() === $this->active) {
                return $editor;
            }
        }

        return new DefaultEditor();
    }

    public function getEditors(): iterable
    {
        return $this->editors;
    }
}

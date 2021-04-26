<?php

declare(strict_types=1);

namespace Tulia\Cms\WysiwygEditor\Core\Application;

/**
 * @author Adam Banaszkiewicz
 */
class Registry implements RegistryInterface
{
    /**
     * @var array|iterable
     */
    protected $editors = [];

    /**
     * @var string|null
     */
    protected $active;

    /**
     * @param iterable $editors
     * @param string|null $active
     */
    public function __construct(iterable $editors, ?string $active = null)
    {
        $this->editors = $editors;
        $this->active  = $active;
    }

    /**
     * {@inheritdoc}
     */
    public function getActiveEditor(): WysiwygEditorInterface
    {
        foreach ($this->editors as $editor) {
            if ($editor->getId() === $this->active) {
                return $editor;
            }
        }

        return new DefaultEditor();
    }

    /**
     * {@inheritdoc}
     */
    public function getEditors(): iterable
    {
        return $this->editors;
    }
}

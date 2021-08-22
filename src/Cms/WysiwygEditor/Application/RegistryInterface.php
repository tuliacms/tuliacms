<?php

declare(strict_types=1);

namespace Tulia\Cms\WysiwygEditor\Application;

/**
 * @author Adam Banaszkiewicz
 */
interface RegistryInterface
{
    public function getActiveEditor(): WysiwygEditorInterface;

    public function getEditors(): iterable;
}

<?php

declare(strict_types=1);

namespace Tulia\Cms\WysiwygEditor\Core\Application;

/**
 * @author Adam Banaszkiewicz
 */
interface RegistryInterface
{
    /**
     * @return WysiwygEditorInterface
     */
    public function getActiveEditor(): WysiwygEditorInterface;

    /**
     * @return iterable
     */
    public function getEditors(): iterable;
}

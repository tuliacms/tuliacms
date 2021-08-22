<?php

declare(strict_types=1);

namespace Tulia\Cms\WysiwygEditor\Application;

use Tulia\Cms\Options\Domain\ReadModel\Options;

/**
 * @author Adam Banaszkiewicz
 */
class RegistryFactory
{
    public static function create(iterable $editors, Options $options): RegistryInterface
    {
        return new Registry($editors, $options->get('wysiwyg_editor'));
    }
}

<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer\Changeset\Transformer;

use Tulia\Component\Theme\Customizer\Changeset\ChangesetInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ChangesetFieldsDefinitionControlsTransformer
{
    /**
     * @param ChangesetInterface $changeset
     * @param iterable $controls
     */
    public function transform(ChangesetInterface $changeset, iterable $controls): void
    {
        foreach ($controls as $control) {
            $changeset->setFieldDefinition($control['id'], [
                'multilingual' => $control['multilingual'] ?? false,
            ]);
        }
    }
}

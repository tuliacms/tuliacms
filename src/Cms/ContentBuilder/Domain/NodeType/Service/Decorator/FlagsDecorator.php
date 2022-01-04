<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\NodeType\Service\Decorator;

use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\ContentBuilder\Domain\ContentType\Model\Field;
use Tulia\Cms\ContentBuilder\Domain\NodeType\Model\NodeType;
use Tulia\Cms\ContentBuilder\Domain\NodeType\Service\NodeTypeDecoratorInterface;
use Tulia\Cms\Node\Domain\NodeFlag\NodeFlagRegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class FlagsDecorator implements NodeTypeDecoratorInterface
{
    private NodeFlagRegistryInterface $flagRegistry;

    private TranslatorInterface $translator;

    public function __construct(NodeFlagRegistryInterface $flagRegistry, TranslatorInterface $translator)
    {
        $this->flagRegistry = $flagRegistry;
        $this->translator = $translator;
    }

    public function decorate(NodeType $nodeType): void
    {
        $nodeType->addField(new Field([
            'code' => 'flags',
            'type' => 'select',
            'name' => 'flags',
            'is_multilingual' => false,
            'is_multiple' => true,
            'is_internal' => true,
            'builder_options' => function () {
                $availableFlags = [];

                foreach ($this->flagRegistry->all() as $type => $flag) {
                    $availableFlags[$this->translator->trans($flag['label'], [], 'node')] = $type;
                }

                return [
                    'translation_domain' => 'node',
                    'choices' => $availableFlags,
                    'help' => 'flagsHelp',
                    'choice_translation_domain' => false,
                    'multiple' => true,
                ];
            }
        ]));
    }
}

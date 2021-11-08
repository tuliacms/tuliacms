<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\NodeType\Service\Decorators;

use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\ContentBuilder\Domain\Field\Model\Field;
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
        $availableFlags = [];

        foreach ($this->flagRegistry->all() as $type => $flag) {
            $availableFlags[$this->translator->trans($flag['label'])] = $type;
        }

        $nodeType->addField(new Field([
            'name' => 'flags',
            'type' => 'select',
            'label' => 'flags',
            'multilingual' => false,
            'multiple' => true,
            'constraints' => [],
            'flags' => [],
            'options' => [
                'choices' => $availableFlags,
                'help' => 'flagsHelp',
                'choice_translation_domain' => false,
                'multiple' => true,
            ]
        ]));
    }
}

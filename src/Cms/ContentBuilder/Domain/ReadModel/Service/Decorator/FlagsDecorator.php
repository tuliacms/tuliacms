<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\ReadModel\Service\Decorator;

use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Service\ContentTypeDecoratorInterface;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\Field;
use Tulia\Cms\Node\Domain\NodeFlag\NodeFlagRegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class FlagsDecorator implements ContentTypeDecoratorInterface
{
    private NodeFlagRegistryInterface $flagRegistry;

    private TranslatorInterface $translator;

    public function __construct(NodeFlagRegistryInterface $flagRegistry, TranslatorInterface $translator)
    {
        $this->flagRegistry = $flagRegistry;
        $this->translator = $translator;
    }

    public function decorate(ContentType $contentType): void
    {
        if ($contentType->isType('node') === false) {
            return;
        }

        $contentType->addField(new Field([
            'code' => 'flags',
            'type' => 'select',
            'name' => 'flags',
            'is_multilingual' => false,
            'is_internal' => true,
            'is_multiple' => true,
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

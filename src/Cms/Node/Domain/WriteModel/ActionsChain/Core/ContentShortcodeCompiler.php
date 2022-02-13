<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\ActionsChain\Core;

use Tulia\Cms\Attributes\Domain\WriteModel\Model\Attribute;
use Tulia\Cms\Node\Domain\WriteModel\ActionsChain\NodeActionInterface;
use Tulia\Cms\Node\Domain\WriteModel\Model\Node;
use Tulia\Component\Shortcode\ProcessorInterface;

/**
 * Listener is responsible for parsing and compiling Node's source
 * content, and saving this content into `content` field on Node.
 * All operations are done while create or update node at backend.
 *
 * @author Adam Banaszkiewicz
 */
class ContentShortcodeCompiler implements NodeActionInterface
{
    protected ProcessorInterface $processor;

    public function __construct(ProcessorInterface $processor)
    {
        $this->processor = $processor;
    }

    public static function supports(): array
    {
        return [
            'insert' => 100,
            'update' => 100,
        ];
    }

    public function execute(Node $node): void
    {
        foreach ($node->getAttributes() as $attribute) {
            if (! $attribute->getValue()) {
                continue;
            }

            if ($attribute->isCompilable() === false) {
                continue;
            }

            $uri = $attribute->produceUriWithModificator('compiled');

            $node->updateAttributes([
                $uri => new Attribute(
                    $attribute->produceCodeWithModificator('compiled'),
                    $this->processor->process($attribute->getValue()),
                    $uri,
                    ['renderable'],
                    $attribute->isMultilingual(),
                    $attribute->hasNonscalarValue()
                )
            ]);
        }
    }
}

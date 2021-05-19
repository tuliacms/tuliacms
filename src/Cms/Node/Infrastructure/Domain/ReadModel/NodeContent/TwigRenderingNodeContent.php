<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Domain\ReadModel\NodeContent;

use Tulia\Cms\Node\Domain\ReadModel\Finder\Model\Node;
use Tulia\Cms\Node\Domain\ReadModel\NodeContent\NodeContentInterface;
use Tulia\Component\Templating\EngineInterface;

/**
 * @author Adam Banaszkiewicz
 */
class TwigRenderingNodeContent implements NodeContentInterface
{
    private Node $node;

    private ?string $source;

    private EngineInterface $engine;

    private string $environment;

    private ?string $rendered = null;

    public function __construct(Node $node, ?string $source, EngineInterface $engine, string $environment)
    {
        $this->node = $node;
        $this->source = $source;
        $this->engine = $engine;
        $this->environment = $environment;
    }

    public function __toString(): string
    {
        if ($this->rendered !== null) {
            return $this->rendered;
        }

        try {
            $this->rendered = $this->engine->renderString(
                $this->source,
                [ 'this' => $this->node ],
                'Node content ' . $this->node->getId()
            );
        } catch (\Throwable $exception) {
            if ($this->environment === 'prod') {
                $this->rendered = 'Cannot render Node content due to an internal content error.';
            } else {
                $e = $exception->getPrevious();

                if (! $e instanceof \Throwable) {
                    $e = $exception;
                }

                $this->rendered .='Cannot render Node content. ';
                $this->rendered .= '<i><b>' . str_replace('Error occured creating string template: ', '', $e->getMessage()) . '</b></i>';
                $this->rendered .= sprintf(
                    "<pre>In %s on line %d.\nTrace:\n%s</pre>",
                    $e->getFile(),
                    $e->getLine(),
                    $e->getTraceAsString()
                );
            }
        }

        return $this->rendered;
    }

    public function getRendered(): string
    {
        return $this->__toString();
    }

    public function setSource(?string $source): void
    {
        $this->source = $source;
        $this->rendered = null;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }
}

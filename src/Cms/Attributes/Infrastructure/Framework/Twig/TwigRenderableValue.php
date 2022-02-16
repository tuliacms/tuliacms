<?php

declare(strict_types=1);

namespace Tulia\Cms\Attributes\Infrastructure\Framework\Twig;

use Tulia\Cms\Attributes\Domain\ReadModel\ValueRender\RenderableValueInterface;
use Tulia\Component\Templating\EngineInterface;

/**
 * @author Adam Banaszkiewicz
 */
class TwigRenderableValue implements RenderableValueInterface
{
    private ?string $source;
    private ?string $rendered = null;
    private array $context;
    private EngineInterface $engine;
    private string $environment;

    public function __construct(?string $source, array $context, EngineInterface $engine, string $environment)
    {
        $this->source = $source;
        $this->context = $context;
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
                [],
                sprintf('Attribute "%s" value', $this->context['attribute'])
            );
        } catch (\Throwable $exception) {
            if ($this->environment === 'prod') {
                $this->rendered = 'Cannot render Attribute value due to an internal content error.';
            } else {
                $e = $exception->getPrevious();

                if (! $e instanceof \Throwable) {
                    $e = $exception;
                }

                $this->rendered .= 'Cannot render Attribute value. ';
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

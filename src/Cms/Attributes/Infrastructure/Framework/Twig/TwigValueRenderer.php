<?php

declare(strict_types=1);

namespace Tulia\Cms\Attributes\Infrastructure\Framework\Twig;

use Tulia\Cms\Attributes\Domain\ReadModel\ValueRender\RenderableValueInterface;
use Tulia\Cms\Attributes\Domain\ReadModel\ValueRender\ValueRendererInterface;
use Tulia\Component\Templating\EngineInterface;

/**
 * @author Adam Banaszkiewicz
 */
class TwigValueRenderer implements ValueRendererInterface
{
    private EngineInterface $engine;
    private string $environment;

    public function __construct(EngineInterface $engine, string $environment)
    {
        $this->engine = $engine;
        $this->environment = $environment;
    }

    public function render(?string $value, array $context): RenderableValueInterface
    {
        return new TwigRenderableValue($value, $context, $this->engine, $this->environment);
    }
}

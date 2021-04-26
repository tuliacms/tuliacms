<?php

declare(strict_types=1);

namespace Tulia\Framework\Twig\Extension;

use Symfony\Component\HttpFoundation\RequestStack;
use Tulia\Framework\Templating\App;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

/**
 * @author Adam Banaszkiewicz
 */
class AppExtension extends AbstractExtension implements GlobalsInterface
{
    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function getGlobals(): array
    {
        return [
            'app' => new App($this->requestStack),
        ];
    }
}

<?php

declare(strict_types=1);

namespace Tulia\Component\Hooking\TwigBridge;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Tulia\Component\Hooking\HookerInterface;

/**
 * @author Adam Banaszkiewicz
 */
class HookingExtension extends AbstractExtension
{
    protected $hooker;

    /**
     * @param HookerInterface $hooker
     */
    public function __construct(HookerInterface $hooker)
    {
        $this->hooker = $hooker;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('do_action', function (string $action, array $arguments = []) {
                return $this->hooker->doAction($action, $arguments);
            }, [
                'is_safe' => [ 'html' ]
            ]),
            new TwigFunction('do_filter', function (string $filter, $content = null, array $arguments = []) {
                return $this->hooker->doFilter($filter, $content, $arguments);
            }, [
                'is_safe' => [ 'html' ]
            ]),
        ];
    }
}

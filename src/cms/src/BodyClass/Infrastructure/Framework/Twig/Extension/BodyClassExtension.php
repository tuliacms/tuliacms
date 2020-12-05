<?php

declare(strict_types=1);

namespace Tulia\Cms\BodyClass\Infrastructure\Framework\Twig\Extension;

use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Tulia\Cms\BodyClass\Application\Event\CollectBodyClassEvent;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class BodyClassExtension extends AbstractExtension
{
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param RequestStack $requestStack
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, RequestStack $requestStack)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->requestStack    = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('body_class', function (array $append = []) {
                if (! $append) {
                    $append = [];
                }

                if (\is_array($append) === false) {
                    $append = [$append];
                }

                $event = new CollectBodyClassEvent($this->requestStack->getMasterRequest(), $append);
                $this->eventDispatcher->dispatch($event);

                return implode(' ', $event->getAll());
            }, [
                'is_safe' => [ 'html' ]
            ]),
        ];
    }
}

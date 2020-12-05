<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Application\EventListener;

use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Node\Infrastructure\NodeType\RegistryInterface;
use Tulia\Cms\Node\Query\Model\Node;
use Tulia\Cms\EditLinks\Application\Event\CollectEditLinksEvent;
use Tulia\Component\Routing\Exception\RouteNotFoundException;
use Tulia\Component\Routing\RouterInterface;

/**
 * @author Adam Banaszkiewicz
 */
class EditLinks
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var RegistryInterface
     */
    protected $registry;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator, RouterInterface $router, RegistryInterface $registry)
    {
        $this->translator = $translator;
        $this->router = $router;
        $this->registry = $registry;
    }

    /**
     * @param CollectEditLinksEvent $event
     *
     * @throws RouteNotFoundException
     */
    public function handle(CollectEditLinksEvent $event): void
    {
        /** @var Node $node */
        $node = $event->getObject();

        if (!$node instanceof Node) {
            return;
        }

        try {
            $type = $this->registry->getType($node->getType());

            $event->add('node.edit', [
                'link'  => $this->router->generate('backend.node.edit', [ 'node_type' => $node->getType(), 'id' => $node->getId() ]),
                'label' => $this->translator->trans('editNode', [
                    'node' => mb_strtolower($this->translator->trans('node', [], $type->getTranslationDomain())),
                ]),
            ]);
        } catch (\Exception $e) {
            // Do nothing when Node Type not exists.
        }
    }
}
